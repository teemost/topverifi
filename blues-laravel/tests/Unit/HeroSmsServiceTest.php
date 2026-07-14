<?php

namespace Tests\Unit;

use App\Services\HeroSmsService;
use Illuminate\Support\Facades\Http;
use Monolog\Handler\NullHandler;
use Tests\TestCase;

/**
 * Unit tests for HeroSmsService.
 *
 * All tests use Http::fake() to mock API responses — no real network calls are made.
 * The tests cover:
 *   - Successful number purchase
 *   - Restricted numbers (must be skipped and retried)
 *   - All other non-usable statuses: unavailable, banned, sold, expired, cancelled
 *   - Retry/backoff logic exhausting all attempts
 *   - Fatal API errors (NO_BALANCE, BAD_KEY etc.)
 *   - HTTP 429 rate limiting
 *   - API timeouts and network failures
 *   - Empty and invalid-JSON responses
 *   - checkSms status parsing for all known codes
 */
class HeroSmsServiceTest extends TestCase
{
    /**
     * Speed up tests: override config so sleeps are nearly instant.
     */
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'herosms.api_key'       => 'test-api-key',
            'herosms.max_retries'   => 3,
            'herosms.base_delay_ms' => 1,   // 1 ms so tests don't actually wait
            'herosms.max_delay_ms'  => 2,
            'herosms.rate_limit_ms' => 1,
            'herosms.timeout'       => 5,
            'herosms.log_channel'   => 'null',
        ]);

        // Route Hero-SMS logs to 'null' driver so they don't pollute test output
        // ('null' is a built-in Laravel channel — no Log::spy() needed)
        config(['logging.channels.null' => ['driver' => 'monolog', 'handler' => \Monolog\Handler\NullHandler::class]]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function make(): HeroSmsService
    {
        return new HeroSmsService();
    }

    /**
     * Return a fake sequence: first getNumber succeeds, then getStatus returns $statusBody.
     */
    private function fakeOrderWithStatus(string $orderId, string $phone, string $statusBody, string $baseUrl = 'https://hero-sms.com/stubs/handler_api.php'): void
    {
        Http::fake([
            $baseUrl . '*' => Http::sequence()
                ->push("ACCESS_NUMBER:{$orderId}:{$phone}", 200) // getNumber
                ->push($statusBody, 200),                         // getStatus
        ]);
    }

    // ── isConfigured ─────────────────────────────────────────────────────────

    public function test_is_configured_returns_true_when_api_key_set(): void
    {
        $this->assertTrue($this->make()->isConfigured());
    }

    public function test_is_configured_returns_false_when_api_key_empty(): void
    {
        config(['herosms.api_key' => '']);
        $svc = new HeroSmsService();
        $this->assertFalse($svc->isConfigured());
    }

    // ── checkSms ─────────────────────────────────────────────────────────────

    public function test_check_sms_returns_completed_when_status_ok(): void
    {
        Http::fake(['*' => Http::response('STATUS_OK:123456', 200)]);

        $result = $this->make()->checkSms('999');

        $this->assertTrue($result['success']);
        $this->assertSame('completed', $result['data']['status_category']);
        $this->assertSame('123456', $result['data']['sms']);
        $this->assertSame(3, $result['data']['status']);
    }

    public function test_check_sms_returns_pending_when_wait_code(): void
    {
        Http::fake(['*' => Http::response('STATUS_WAIT_CODE', 200)]);

        $result = $this->make()->checkSms('999');

        $this->assertTrue($result['success']);
        $this->assertSame('pending', $result['data']['status_category']);
        $this->assertNull($result['data']['sms']);
        $this->assertSame(1, $result['data']['status']);
    }

    public function test_check_sms_returns_pending_when_wait_resend(): void
    {
        Http::fake(['*' => Http::response('STATUS_WAIT_RESEND', 200)]);
        $result = $this->make()->checkSms('999');
        $this->assertSame('pending', $result['data']['status_category']);
    }

    public function test_check_sms_returns_pending_when_status_pending(): void
    {
        Http::fake(['*' => Http::response('STATUS_PENDING', 200)]);
        $result = $this->make()->checkSms('999');
        $this->assertSame('pending', $result['data']['status_category']);
    }

    public function test_check_sms_returns_cancelled(): void
    {
        Http::fake(['*' => Http::response('STATUS_CANCEL', 200)]);

        $result = $this->make()->checkSms('999');

        $this->assertTrue($result['success']);
        $this->assertSame('cancelled', $result['data']['status_category']);
        $this->assertSame(6, $result['data']['status']);
    }

    public function test_check_sms_returns_restricted(): void
    {
        Http::fake(['*' => Http::response('STATUS_RESTRICTED', 200)]);

        $result = $this->make()->checkSms('999');

        $this->assertTrue($result['success']);
        $this->assertSame('restricted', $result['data']['status_category']);
        $this->assertSame(6, $result['data']['status']);
    }

    public function test_check_sms_returns_unavailable(): void
    {
        Http::fake(['*' => Http::response('STATUS_UNAVAILABLE', 200)]);
        $result = $this->make()->checkSms('999');
        $this->assertSame('unavailable', $result['data']['status_category']);
    }

    public function test_check_sms_returns_banned(): void
    {
        Http::fake(['*' => Http::response('STATUS_BANNED', 200)]);
        $result = $this->make()->checkSms('999');
        $this->assertSame('banned', $result['data']['status_category']);
    }

    public function test_check_sms_returns_sold(): void
    {
        Http::fake(['*' => Http::response('STATUS_SOLD', 200)]);
        $result = $this->make()->checkSms('999');
        $this->assertSame('sold', $result['data']['status_category']);
    }

    public function test_check_sms_returns_expired(): void
    {
        Http::fake(['*' => Http::response('STATUS_EXPIRED', 200)]);
        $result = $this->make()->checkSms('999');
        $this->assertSame('expired', $result['data']['status_category']);
    }

    public function test_check_sms_returns_invalid(): void
    {
        Http::fake(['*' => Http::response('STATUS_INVALID', 200)]);
        $result = $this->make()->checkSms('999');
        $this->assertSame('invalid', $result['data']['status_category']);
    }

    public function test_check_sms_returns_failed(): void
    {
        Http::fake(['*' => Http::response('STATUS_FAILED', 200)]);
        $result = $this->make()->checkSms('999');
        $this->assertSame('failed', $result['data']['status_category']);
    }

    // ── orderNumber — success ─────────────────────────────────────────────────

    public function test_order_number_succeeds_with_pending_status(): void
    {
        $this->fakeOrderWithStatus('42', '79001234567', 'STATUS_WAIT_CODE');

        $result = $this->make()->orderNumber('0', 'wa');

        $this->assertTrue($result['success']);
        $this->assertSame('42', $result['data']['order_id']);
        $this->assertSame('79001234567', $result['data']['number']);
    }

    public function test_order_number_succeeds_with_completed_status(): void
    {
        $this->fakeOrderWithStatus('55', '79009999999', 'STATUS_OK:654321');

        $result = $this->make()->orderNumber('0', 'tg');

        $this->assertTrue($result['success']);
        $this->assertSame('55', $result['data']['order_id']);
    }

    // ── orderNumber — restricted numbers ─────────────────────────────────────

    public function test_order_number_retries_when_restricted_and_eventually_succeeds(): void
    {
        // Attempt 1: getNumber OK but status is RESTRICTED
        // Attempt 2: getNumber OK and status is WAIT_CODE (usable)
        Http::fake([
            '*' => Http::sequence()
                ->push('ACCESS_NUMBER:10:79001111111', 200) // attempt 1 getNumber
                ->push('STATUS_RESTRICTED', 200)             // attempt 1 getStatus
                ->push('ACCESS_NUMBER:11:79002222222', 200) // attempt 2 getNumber
                ->push('STATUS_WAIT_CODE', 200),             // attempt 2 getStatus
        ]);

        $result = $this->make()->orderNumber('0', 'wa', 3);

        $this->assertTrue($result['success']);
        // Must return the SECOND (usable) number, not the restricted one
        $this->assertSame('11', $result['data']['order_id']);
        $this->assertSame('79002222222', $result['data']['number']);
    }

    public function test_order_number_never_returns_restricted_number(): void
    {
        // All three attempts return restricted numbers
        Http::fake([
            '*' => Http::sequence()
                ->push('ACCESS_NUMBER:10:79001111111', 200)
                ->push('STATUS_RESTRICTED', 200)
                ->push('ACCESS_NUMBER:11:79002222222', 200)
                ->push('STATUS_RESTRICTED', 200)
                ->push('ACCESS_NUMBER:12:79003333333', 200)
                ->push('STATUS_RESTRICTED', 200),
        ]);

        $result = $this->make()->orderNumber('0', 'wa', 3);

        $this->assertFalse($result['success']);
        $this->assertStringContainsStringIgnoringCase('restricted', $result['message']);
    }

    // ── orderNumber — other retryable statuses ────────────────────────────────

    /**
     * @dataProvider retryableStatusProvider
     */
    public function test_order_number_retries_on_retryable_status(string $statusBody, string $expectedCategory): void
    {
        // First attempt returns a non-usable number, second returns a good one
        Http::fake([
            '*' => Http::sequence()
                ->push('ACCESS_NUMBER:20:79001111111', 200)
                ->push($statusBody, 200)
                ->push('ACCESS_NUMBER:21:79002222222', 200)
                ->push('STATUS_WAIT_CODE', 200),
        ]);

        $result = $this->make()->orderNumber('0', 'fb', 3);

        $this->assertTrue($result['success'], "Should retry on {$expectedCategory} and succeed on the second attempt");
        $this->assertSame('21', $result['data']['order_id']);
    }

    public static function retryableStatusProvider(): array
    {
        return [
            'unavailable' => ['STATUS_UNAVAILABLE', 'unavailable'],
            'banned'      => ['STATUS_BANNED',      'banned'],
            'sold'        => ['STATUS_SOLD',         'sold'],
            'expired'     => ['STATUS_EXPIRED',      'expired'],
            'cancelled'   => ['STATUS_CANCEL',       'cancelled'],
        ];
    }

    // ── orderNumber — NO_NUMBERS ──────────────────────────────────────────────

    public function test_order_number_retries_on_no_numbers(): void
    {
        Http::fake([
            '*' => Http::sequence()
                ->push('NO_NUMBERS', 200)
                ->push('NO_NUMBERS', 200)
                ->push('ACCESS_NUMBER:99:79005555555', 200)
                ->push('STATUS_WAIT_CODE', 200),
        ]);

        $result = $this->make()->orderNumber('0', 'wa', 3);

        $this->assertTrue($result['success']);
        $this->assertSame('99', $result['data']['order_id']);
    }

    public function test_order_number_fails_after_exhausting_retries_on_no_numbers(): void
    {
        Http::fake(['*' => Http::response('NO_NUMBERS', 200)]);

        $result = $this->make()->orderNumber('0', 'wa', 3);

        $this->assertFalse($result['success']);
        $this->assertStringContainsStringIgnoringCase('tried 3 time', $result['message']);
    }

    // ── orderNumber — fatal errors ────────────────────────────────────────────

    public function test_order_number_fails_immediately_on_no_balance(): void
    {
        Http::fake(['*' => Http::response('NO_BALANCE', 200)]);

        $result = $this->make()->orderNumber('0', 'wa', 3);

        $this->assertFalse($result['success']);
        $this->assertStringContainsStringIgnoringCase('balance', $result['message']);
        // Should only have called the API once (no retry for fatal errors)
        Http::assertSentCount(1);
    }

    public function test_order_number_fails_immediately_on_bad_key(): void
    {
        Http::fake(['*' => Http::response('BAD_KEY', 200)]);

        $result = $this->make()->orderNumber('0', 'wa', 3);

        $this->assertFalse($result['success']);
        $this->assertStringContainsStringIgnoringCase('invalid', $result['message']);
        Http::assertSentCount(1);
    }

    public function test_order_number_fails_immediately_on_bad_service(): void
    {
        Http::fake(['*' => Http::response('BAD_SERVICE', 200)]);

        $result = $this->make()->orderNumber('0', 'invalid_service', 3);

        $this->assertFalse($result['success']);
        Http::assertSentCount(1);
    }

    public function test_order_number_fails_immediately_on_bad_country(): void
    {
        Http::fake(['*' => Http::response('BAD_COUNTRY', 200)]);

        $result = $this->make()->orderNumber('9999', 'wa', 3);

        $this->assertFalse($result['success']);
        Http::assertSentCount(1);
    }

    // ── HTTP error handling ───────────────────────────────────────────────────

    public function test_order_number_fails_on_http_500(): void
    {
        Http::fake(['*' => Http::response('Internal Server Error', 500)]);

        $result = $this->make()->orderNumber('0', 'wa', 3);

        $this->assertFalse($result['success']);
        $this->assertStringContainsStringIgnoringCase('HTTP 500', $result['message']);
    }

    public function test_order_number_handles_rate_limiting_429(): void
    {
        // 429 on first getNumber attempt, then success on second
        Http::fake([
            '*' => Http::sequence()
                ->push('Rate limit exceeded', 429)
                ->push('ACCESS_NUMBER:77:79007777777', 200)
                ->push('STATUS_WAIT_CODE', 200),
        ]);

        $result = $this->make()->orderNumber('0', 'wa', 3);

        $this->assertTrue($result['success']);
        $this->assertSame('77', $result['data']['order_id']);
    }

    public function test_order_number_fails_gracefully_on_network_error(): void
    {
        Http::fake(['*' => function () {
            throw new \Illuminate\Http\Client\ConnectionException('Connection refused');
        }]);

        $result = $this->make()->orderNumber('0', 'wa', 1);

        $this->assertFalse($result['success']);
        $this->assertStringContainsStringIgnoringCase('connection', $result['message']);
    }

    // ── Empty / invalid JSON ──────────────────────────────────────────────────

    public function test_call_fails_gracefully_on_empty_response(): void
    {
        Http::fake(['*' => Http::response('', 200)]);

        $result = $this->make()->getBalance();

        $this->assertFalse($result['success']);
        $this->assertStringContainsStringIgnoringCase('empty', $result['message']);
    }

    public function test_parsePlain_handles_invalid_json(): void
    {
        // getBalance is a simple single-call method — use it to exercise parsePlain with bad JSON
        Http::fake(['*' => Http::response('{not-valid-json', 200)]);

        $result = $this->make()->getBalance();

        // Should fail gracefully, not throw
        $this->assertFalse($result['success']);
    }

    // ── getBalance ────────────────────────────────────────────────────────────

    public function test_get_balance_parses_correctly(): void
    {
        Http::fake(['*' => Http::response('ACCESS_BALANCE:42.50', 200)]);

        $result = $this->make()->getBalance();

        $this->assertTrue($result['success']);
        $this->assertSame(42.50, $result['data']['balance']);
    }

    public function test_get_balance_fails_on_bad_key(): void
    {
        Http::fake(['*' => Http::response('{"title":"BAD_KEY","details":"Invalid API key"}', 200)]);

        $result = $this->make()->getBalance();

        $this->assertFalse($result['success']);
        $this->assertStringContainsStringIgnoringCase('Invalid API key', $result['message']);
    }

    // ── serviceName ───────────────────────────────────────────────────────────

    public function test_service_name_returns_known_name(): void
    {
        $this->assertSame('WhatsApp', HeroSmsService::serviceName('wa'));
        $this->assertSame('Telegram', HeroSmsService::serviceName('tg'));
        $this->assertSame('Instagram', HeroSmsService::serviceName('ig'));
    }

    public function test_service_name_returns_ucfirst_for_unknown_abbreviation(): void
    {
        $this->assertSame('Zzz', HeroSmsService::serviceName('zzz'));
    }
}
