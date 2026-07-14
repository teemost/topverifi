<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use App\Models\Wallet;
use App\Services\HeroSmsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Monolog\Handler\NullHandler;
use Tests\TestCase;

/**
 * Feature tests for the Hero-SMS virtual-number order flow.
 *
 * Tests the full HTTP request → controller → service → DB layer.
 * All Hero-SMS API calls are mocked with Http::fake() — no real network traffic.
 */
class HeroSmsOrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure Hero-SMS is configured and server2 is enabled for all tests
        config([
            'herosms.api_key'       => 'test-api-key',
            'herosms.max_retries'   => 3,
            'herosms.base_delay_ms' => 1,
            'herosms.max_delay_ms'  => 2,
            'herosms.rate_limit_ms' => 1,
            'herosms.log_channel'   => 'null',
        ]);

        Setting::create(['key' => 'virtual_number_enabled', 'value' => '1']);
        Setting::create(['key' => 'server2_enabled',         'value' => '1']);
        Setting::create(['key' => 'vn_commission_type',      'value' => 'flat']);
        Setting::create(['key' => 'vn_commission_value',     'value' => '0']);
        Setting::create(['key' => 'usd_to_ngn_rate',         'value' => '1600']);

        config(['logging.channels.null' => ['driver' => 'monolog', 'handler' => \Monolog\Handler\NullHandler::class]]);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function actingAsUser(float $walletBalance = 500.0): User
    {
        $user   = User::factory()->create();
        $wallet = Wallet::create(['user_id' => $user->id, 'balance' => $walletBalance]);
        return $user;
    }

    private function orderPayload(array $overrides = []): array
    {
        return array_merge([
            'provider'     => 'herosms',
            'service_id'   => 'wa',
            'country'      => '0',
            'price'        => '0.15',
            'service_name' => 'WhatsApp',
        ], $overrides);
    }

    // ── Successful purchase ───────────────────────────────────────────────────

    public function test_successful_purchase_creates_order_and_debits_wallet(): void
    {
        $user = $this->actingAsUser(100.0);

        Http::fake([
            '*' => Http::sequence()
                ->push('ACCESS_NUMBER:42:79001234567', 200) // getNumber
                ->push('STATUS_WAIT_CODE', 200),             // getStatus (status check)
        ]);

        $response = $this->actingAs($user)
            ->post(route('dashboard.virtual-numbers.order'), $this->orderPayload(['price' => '0.15']));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Order should be in DB
        $this->assertDatabaseHas('virtual_number_orders', [
            'user_id'           => $user->id,
            'provider'          => 'herosms',
            'external_order_id' => '42',
            'phone_number'      => '79001234567',
            'status'            => 'active',
        ]);

        // Wallet should be debited
        $this->assertDatabaseHas('wallets', [
            'user_id' => $user->id,
            'balance' => 99.85, // 100 - 0.15
        ]);
    }

    // ── Restricted number handling ────────────────────────────────────────────

    public function test_restricted_number_triggers_retry_and_returns_good_number(): void
    {
        $user = $this->actingAsUser(100.0);

        Http::fake([
            '*' => Http::sequence()
                ->push('ACCESS_NUMBER:10:79001111111', 200) // attempt 1 getNumber
                ->push('STATUS_RESTRICTED', 200)             // attempt 1 getStatus → restricted
                ->push('ACCESS_NUMBER:11:79002222222', 200) // attempt 2 getNumber
                ->push('STATUS_WAIT_CODE', 200),             // attempt 2 getStatus → good
        ]);

        $response = $this->actingAs($user)
            ->post(route('dashboard.virtual-numbers.order'), $this->orderPayload());

        $response->assertSessionHas('success');

        // Must store the SECOND (good) number, not the restricted one
        $this->assertDatabaseHas('virtual_number_orders', [
            'user_id'           => $user->id,
            'external_order_id' => '11',
            'phone_number'      => '79002222222',
        ]);

        $this->assertDatabaseMissing('virtual_number_orders', [
            'external_order_id' => '10',
        ]);
    }

    public function test_all_restricted_numbers_returns_error_to_user(): void
    {
        $user = $this->actingAsUser(100.0);

        // Every attempt gets a restricted number
        Http::fake([
            '*' => Http::sequence()
                ->push('ACCESS_NUMBER:10:79001111111', 200)
                ->push('STATUS_RESTRICTED', 200)
                ->push('ACCESS_NUMBER:11:79002222222', 200)
                ->push('STATUS_RESTRICTED', 200)
                ->push('ACCESS_NUMBER:12:79003333333', 200)
                ->push('STATUS_RESTRICTED', 200),
        ]);

        $response = $this->actingAs($user)
            ->post(route('dashboard.virtual-numbers.order'), $this->orderPayload());

        $response->assertSessionHas('error');

        // No order should be created
        $this->assertDatabaseCount('virtual_number_orders', 0);

        // Wallet should NOT be debited
        $this->assertDatabaseHas('wallets', [
            'user_id' => $user->id,
            'balance' => 100.0,
        ]);
    }

    // ── API failure scenarios ─────────────────────────────────────────────────

    public function test_insufficient_balance_on_herosms_returns_error(): void
    {
        $user = $this->actingAsUser(100.0);

        Http::fake(['*' => Http::response('NO_BALANCE', 200)]);

        $response = $this->actingAs($user)
            ->post(route('dashboard.virtual-numbers.order'), $this->orderPayload());

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('virtual_number_orders', 0);
    }

    public function test_no_numbers_available_returns_error_after_retries(): void
    {
        $user = $this->actingAsUser(100.0);

        Http::fake(['*' => Http::response('NO_NUMBERS', 200)]);

        $response = $this->actingAs($user)
            ->post(route('dashboard.virtual-numbers.order'), $this->orderPayload());

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('virtual_number_orders', 0);
    }

    public function test_network_failure_returns_error(): void
    {
        $user = $this->actingAsUser(100.0);

        Http::fake(['*' => function () {
            throw new \Illuminate\Http\Client\ConnectionException('Connection refused');
        }]);

        $response = $this->actingAs($user)
            ->post(route('dashboard.virtual-numbers.order'), $this->orderPayload());

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('virtual_number_orders', 0);
    }

    // ── Rate limiting ─────────────────────────────────────────────────────────

    public function test_rate_limited_response_is_retried_and_succeeds(): void
    {
        $user = $this->actingAsUser(100.0);

        Http::fake([
            '*' => Http::sequence()
                ->push('Rate limit exceeded', 429)           // attempt 1 → rate limited
                ->push('ACCESS_NUMBER:88:79008888888', 200)  // attempt 2 getNumber
                ->push('STATUS_WAIT_CODE', 200),             // attempt 2 getStatus
        ]);

        $response = $this->actingAs($user)
            ->post(route('dashboard.virtual-numbers.order'), $this->orderPayload());

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('virtual_number_orders', [
            'external_order_id' => '88',
        ]);
    }

    // ── Insufficient wallet balance (application-level) ───────────────────────

    public function test_insufficient_wallet_balance_blocks_order(): void
    {
        // Wallet balance is 0.05, order price is 0.15
        $user = $this->actingAsUser(0.05);

        // Http::fake not needed — should fail before calling the API
        $response = $this->actingAs($user)
            ->post(route('dashboard.virtual-numbers.order'), $this->orderPayload(['price' => '0.15']));

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('virtual_number_orders', 0);
    }

    // ── Cancel / refund ───────────────────────────────────────────────────────

    public function test_cancel_order_refunds_wallet(): void
    {
        $user = $this->actingAsUser(100.0);

        // Create an active order directly in the DB
        $order = \App\Models\VirtualNumberOrder::create([
            'user_id'           => $user->id,
            'provider'          => 'herosms',
            'external_order_id' => '99',
            'service'           => 'WhatsApp',
            'country'           => '0',
            'phone_number'      => '79009999999',
            'cost'              => 50.0,
            'status'            => 'active',
        ]);

        Http::fake(['*' => Http::response('ACCESS_CANCEL', 200)]);

        $response = $this->actingAs($user)
            ->delete(route('dashboard.virtual-numbers.cancel', $order->id));

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('virtual_number_orders', ['id' => $order->id, 'status' => 'cancelled']);

        // Wallet should be refunded
        $this->assertDatabaseHas('wallets', [
            'user_id' => $user->id,
            'balance' => 150.0, // 100 + 50 refund
        ]);
    }

    // ── Timeout scenario ──────────────────────────────────────────────────────

    public function test_api_timeout_returns_meaningful_error(): void
    {
        $user = $this->actingAsUser(100.0);

        Http::fake(['*' => function () {
            throw new \Illuminate\Http\Client\ConnectionException('cURL error 28: Operation timed out');
        }]);

        $response = $this->actingAs($user)
            ->post(route('dashboard.virtual-numbers.order'), $this->orderPayload());

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('virtual_number_orders', 0);
    }
}
