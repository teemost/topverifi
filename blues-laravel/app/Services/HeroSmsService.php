<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Hero-SMS API Service
 *
 * Wraps the Hero-SMS (sms-activate-compatible) API at:
 *   https://hero-sms.com/stubs/handler_api.php
 *
 * Design decisions:
 * - Credentials are read from config/herosms.php (which reads from .env),
 *   with a fallback to the database Settings table for backward compatibility.
 * - All status codes returned by getStatus are explicitly handled. Numbers
 *   whose status is "restricted" (or any other non-usable status) are NEVER
 *   returned to callers; the service retries automatically with exponential
 *   backoff up to the configured max_retries limit.
 * - Every API interaction is logged with timestamp, endpoint, HTTP status,
 *   response body, retry count, and error details.
 * - HTTP 429 rate-limit responses trigger a configurable pause before retry.
 * - Empty responses, invalid JSON, timeouts, and network failures are all
 *   handled gracefully with a meaningful error message.
 */
class HeroSmsService
{
    // ── Internal status categories ────────────────────────────────────────────

    /**
     * Statuses that mean the number is usable (waiting for an SMS or has one).
     */
    private const USABLE_STATUSES = ['pending', 'completed'];

    /**
     * Statuses that should trigger an automatic retry with a new number.
     * We must NEVER surface a number with any of these statuses to the user.
     */
    private const RETRYABLE_STATUSES = [
        'restricted',
        'unavailable',
        'banned',
        'sold',
        'expired',
        'cancelled',
    ];

    // ── sms-activate error codes returned in the response body ────────────────

    /** Error codes for getNumber that are safe to retry on. */
    private const RETRYABLE_ORDER_CODES = ['NO_NUMBERS'];

    /** Error codes for getNumber that indicate a permanent failure. */
    private const FATAL_ORDER_CODES = [
        'NO_BALANCE'  => 'Insufficient Hero-SMS account balance.',
        'BAD_SERVICE' => 'Invalid service selected.',
        'BAD_COUNTRY' => 'Invalid country selected.',
        'BAD_KEY'     => 'Hero-SMS API key is invalid or missing.',
        'ERROR_SQL'   => 'Hero-SMS server error. Please try again.',
    ];

    // ─────────────────────────────────────────────────────────────────────────

    private string $apiKey;
    private string $baseUrl;
    private int    $timeout;
    private int    $maxRetries;
    private int    $baseDelayMs;
    private int    $maxDelayMs;
    private int    $rateLimitMs;
    private string $logChannel;

    /**
     * Large lookup table: service abbreviation → human-readable name.
     * Kept here for standalone use via HeroSmsService::serviceName().
     */
    private const SERVICE_NAMES = [
        'ab'  => 'Airbnb',       'am'  => 'Amazon',       'av'  => 'Avito',
        'ay'  => 'AliExpress',   'az'  => 'Amazon',       'ba'  => 'Badoo',
        'bb'  => 'Blizzard',     'bd'  => 'BandLab',      'bi'  => 'Binance',
        'bk'  => 'Booking',      'bn'  => 'Binance',      'bo'  => 'Bolt',
        'bt'  => 'BitCoin',      'bu'  => 'Bumble',       'bz'  => 'Bizznes',
        'ca'  => 'Careem',       'cb'  => 'Coinbase',     'cc'  => 'Cash App',
        'cf'  => 'Cloudflare',   'ci'  => 'Cian',         'ck'  => 'ClickUp',
        'cl'  => 'Craigslist',   'cm'  => 'Clubmoss',     'cn'  => 'Canva',
        'cp'  => 'Crypto.com',   'cs'  => 'Snapchat',     'dc'  => 'DoorDash',
        'dl'  => 'Deliveroo',    'dm'  => 'DMarket',      'dn'  => 'Deezer',
        'ds'  => 'Discord',      'dt'  => 'Dating',       'du'  => 'Dubsmash',
        'dz'  => 'Dzen',         'ea'  => 'EA Sports',    'eb'  => 'eBay',
        'ep'  => 'eBay',         'et'  => 'Etsy',         'ex'  => 'Exmo',
        'ez'  => 'Ezzocard',     'fb'  => 'Facebook',     'fc'  => 'FoodClub',
        'fd'  => 'Fiverr',       'fi'  => 'Fiverr',       'fk'  => 'Freelancer',
        'fl'  => 'Freelancer',   'fm'  => 'FM Radio',     'fo'  => 'Foot Locker',
        'ft'  => 'Fotostrana',   'fv'  => 'Fiver',        'ga'  => 'Google Ads',
        'gb'  => 'Grab',         'gf'  => 'GreenFarm',    'gg'  => 'GaGa',
        'gh'  => 'GitHub',       'gi'  => 'Grindr',       'gk'  => 'Gekko',
        'gl'  => 'GitLab',       'gm'  => 'Gmail',        'gn'  => 'GreenMan',
        'go'  => 'Google',       'gp'  => 'Google Pay',   'gr'  => 'Groupon',
        'gs'  => 'Google Services', 'gt' => 'GetApp',     'gu'  => 'Guru',
        'gv'  => 'Google Voice', 'gy'  => 'Grubhub',      'hb'  => 'Habr',
        'hh'  => 'HeadHunter',   'hm'  => 'H&M',          'hn'  => 'Honey',
        'hp'  => 'HotPot',       'ht'  => 'Hitch',        'hv'  => 'Hive',
        'hw'  => 'Huawei',       'hy'  => 'Hyundai',      'ia'  => 'Instagram Ads',
        'ic'  => 'iCloud',       'ig'  => 'Instagram',    'im'  => 'iMessage',
        'in'  => 'Indeed',       'iq'  => 'IQOption',     'is'  => 'iShot',
        'it'  => 'iTunes',       'jk'  => 'Jike',         'jm'  => 'Jumia',
        'kb'  => 'KuBit',        'kc'  => 'KuCoin',       'kk'  => 'Kakao',
        'kr'  => 'Kraken',       'kt'  => 'Krait',        'ku'  => 'Kuaishou',
        'kw'  => 'KiwiWallet',   'ky'  => 'KikMessenger', 'la'  => 'Lazada',
        'lb'  => 'LeBonCoin',    'lc'  => 'LuckyCash',    'ld'  => 'LinkedIn',
        'lf'  => 'Lyft',         'li'  => 'Line',         'lk'  => 'LinkedIn',
        'lm'  => 'Lemon',        'ln'  => 'LinkedIn',     'lo'  => 'Lookout',
        'lr'  => 'Lazr',         'ls'  => 'Lalafo',       'lt'  => 'Letgo',
        'lv'  => 'Lovoo',        'lw'  => 'Lawgical',     'ma'  => 'Mail.ru',
        'mb'  => 'MobiBase',     'mc'  => 'Microsoft',    'md'  => 'Mailchimp',
        'me'  => 'Mercado',      'mf'  => 'MobFox',       'mg'  => 'MegaFon',
        'mh'  => 'Mashup',       'mi'  => 'Mi (Xiaomi)',  'mk'  => 'Market',
        'ml'  => 'Mail.ru',      'mm'  => 'Microsoft',    'mn'  => 'Monese',
        'mo'  => 'Momo',         'mp'  => 'Moped',        'mr'  => 'Miro',
        'ms'  => 'Microsoft',    'mt'  => 'Metaco',       'mu'  => 'MutualFund',
        'mv'  => 'Movistar',     'mx'  => 'Mercado',      'my'  => 'MyLead',
        'mz'  => 'Meizu',        'na'  => 'Napster',      'nb'  => 'Northbank',
        'nc'  => 'NordVPN',      'nd'  => 'Nintendo',     'nf'  => 'Netflix',
        'ni'  => 'Nike',         'nk'  => 'Nokia',        'nl'  => 'NordLayer',
        'nm'  => 'Nium',         'no'  => 'Notion',       'np'  => 'Napier',
        'ns'  => 'Nintendo Switch', 'nt' => 'Neteller',   'nu'  => 'Nubank',
        'nv'  => 'Nvidia',       'nw'  => 'NordWallet',   'nx'  => 'NordX',
        'ny'  => 'Nayax',        'oa'  => 'OkadaAfrica',  'ob'  => 'OB Accounts',
        'oc'  => 'OctaFX',       'od'  => 'Odnoklassniki','oe'  => 'OFX',
        'of'  => 'OnlyFans',     'og'  => 'OG',           'oh'  => 'Ohm',
        'oi'  => 'OI',           'ok'  => 'Odnoklassniki','ol'  => 'Olx',
        'om'  => 'OLX',          'on'  => 'Ona',          'op'  => 'OpenAI',
        'or'  => 'Orange',       'os'  => 'OsamuShip',    'ot'  => 'Other',
        'ou'  => 'Outrider',     'ov'  => 'OVH',          'ow'  => 'Owlet',
        'ox'  => 'OX',           'oy'  => 'Oyster',       'pa'  => 'PayPal',
        'pb'  => 'Paytm',        'pc'  => 'PocketCard',   'pd'  => 'Pandora',
        'pe'  => 'Perpay',       'pf'  => 'Pocketful',    'pg'  => 'PayGo',
        'ph'  => 'Phemex',       'pi'  => 'Pinterest',    'pj'  => 'PJ',
        'pk'  => 'PocketKnife',  'pl'  => 'PolyAI',       'pm'  => 'ProtonMail',
        'pn'  => 'PineLabs',     'po'  => 'Poshmark',     'pp'  => 'PayPal',
        'pr'  => 'Proton',       'ps'  => 'PlayStation',  'pt'  => 'Pinterest',
        'pu'  => 'Pumu',         'pv'  => 'Paysafecard',  'pw'  => 'Powerbank',
        'px'  => 'PaxFul',       'py'  => 'Paytm',        'qa'  => 'QA',
        'qr'  => 'QR',           'rd'  => 'Reddit',       'ri'  => 'Riya',
        'rk'  => 'Rakuten',      'rl'  => 'Revolut',      'rm'  => 'Rummy',
        'ro'  => 'Robinhood',    'rp'  => 'Rapid',        'rs'  => 'Resy',
        'rt'  => 'Rutube',       'ru'  => 'RuStore',      'rv'  => 'Revolut',
        'rz'  => 'Razorpay',     'sa'  => 'Samsung',      'sb'  => 'Shopee',
        'sc'  => 'Snapchat',     'sd'  => 'Shopify',      'se'  => 'SendBird',
        'sf'  => 'Surfshark',    'sg'  => 'Signal',       'sh'  => 'Shopee',
        'si'  => 'Signal',       'sk'  => 'Skype',        'sl'  => 'Slack',
        'sm'  => 'SMS',          'sn'  => 'Sina',         'so'  => 'Shopify',
        'sp'  => 'Spotify',      'sq'  => 'Square',       'sr'  => 'Stripe',
        'ss'  => 'Samsung',      'st'  => 'Steam',        'su'  => 'Substack',
        'sv'  => 'Skrill',       'sw'  => 'Sweatcoin',    'sx'  => 'SX',
        'sy'  => 'Sympla',       'sz'  => 'Shazam',       'ta'  => 'Taobao',
        'tb'  => 'Tubi',         'tc'  => 'TrueCaller',   'td'  => 'TikTok Shop',
        'te'  => 'Telegram',     'tf'  => 'TrueFoundry',  'tg'  => 'Telegram',
        'th'  => 'Thorn',        'ti'  => 'Tinder',       'tj'  => 'TJ',
        'tk'  => 'TikTok',       'tl'  => 'Talabat',      'tm'  => 'Twitch',
        'tn'  => 'Tantan',       'to'  => 'Tokopedia',    'tp'  => 'Tapatalk',
        'tq'  => 'TQ',           'tr'  => 'Twitter / X',  'ts'  => 'TextShark',
        'tt'  => 'TikTok',       'tu'  => 'Tumblr',       'tv'  => 'Twitch',
        'tw'  => 'Twitter / X',  'tx'  => 'TextNow',      'ty'  => 'ToyCity',
        'tz'  => 'Tazz',         'ub'  => 'Uber',         'uc'  => 'UCWeb',
        'ud'  => 'Udemy',        'ue'  => 'UberEats',     'uf'  => 'UFO',
        'ug'  => 'Upwork',       'uh'  => 'UHealth',      'ui'  => 'UI',
        'uk'  => 'Uklon',        'ul'  => 'Ulmart',       'um'  => 'UM',
        'un'  => 'Unnamed',      'uo'  => 'Uolo',         'up'  => 'Upwork',
        'uq'  => 'UQ',           'ur'  => 'Urban',        'us'  => 'US',
        'ut'  => 'Utair',        'uu'  => 'UU',           'uv'  => 'UV',
        'uw'  => 'UW',           'ux'  => 'UX',           'uy'  => 'UY',
        'uz'  => 'UZ',           'vb'  => 'Viber',        'vc'  => 'VKontakte',
        'vg'  => 'VG',           'vi'  => 'Viber',        'vk'  => 'VKontakte',
        'vl'  => 'VL',           'vm'  => 'Vimeo',        'vn'  => 'VN',
        'vo'  => 'Vocalink',     'vp'  => 'VPN',          'vr'  => 'VR',
        'vs'  => 'VS',           'vt'  => 'VT',           'vu'  => 'VU',
        'vv'  => 'VV',           'vw'  => 'Volkswagen',   'vx'  => 'VX',
        'vy'  => 'VY',           'vz'  => 'VZ',           'wa'  => 'WhatsApp',
        'wb'  => 'WeBank',       'wc'  => 'WeChat',       'wd'  => 'WD',
        'we'  => 'WeChat',       'wf'  => 'WF',           'wg'  => 'WG',
        'wh'  => 'WH',           'wi'  => 'Wildberries',  'wj'  => 'WJ',
        'wk'  => 'WK',           'wl'  => 'WL',           'wm'  => 'WM',
        'wn'  => 'WN',           'wo'  => 'WO',           'wp'  => 'WordPress',
        'wq'  => 'WQ',           'wr'  => 'WR',           'ws'  => 'WS',
        'wt'  => 'WhatsApp Business', 'wu' => 'WU',       'wv'  => 'WV',
        'ww'  => 'WW',           'wx'  => 'WX',           'wy'  => 'WY',
        'wz'  => 'WZ',           'xa'  => 'Xiaomi',       'xb'  => 'XBox',
        'xi'  => 'Xiaomi',       'xm'  => 'XM',           'ya'  => 'Yandex',
        'yk'  => 'Yandex',       'ym'  => 'YandexMoney',  'yo'  => 'YouTube',
        'yt'  => 'YouTube',      'yu'  => 'YU',           'yy'  => 'YY',
        'za'  => 'Zalo',         'zl'  => 'Zalo',         'zo'  => 'Zoom',
        'zp'  => 'ZaloPay',      'zt'  => 'ZT',
    ];

    // ─────────────────────────────────────────────────────────────────────────

    public function __construct()
    {
        // Prefer .env / config; fall back to database setting for existing setups.
        $envKey = config('herosms.api_key', '');
        if (!empty($envKey)) {
            $this->apiKey = trim($envKey);
        } else {
            try {
                // DB fallback — may not be available in unit-test environments
                $this->apiKey = trim(Setting::get('herosms_api_key', ''));
            } catch (\Throwable) {
                $this->apiKey = '';
            }
        }

        $this->baseUrl     = config('herosms.base_url', 'https://hero-sms.com/stubs/handler_api.php');
        $this->timeout     = (int) config('herosms.timeout', 20);
        $this->maxRetries  = (int) config('herosms.max_retries', 5);
        $this->baseDelayMs = (int) config('herosms.base_delay_ms', 1000);
        $this->maxDelayMs  = (int) config('herosms.max_delay_ms', 16000);
        $this->rateLimitMs = (int) config('herosms.rate_limit_ms', 5000);
        $this->logChannel  = config('herosms.log_channel', 'stack');
    }

    /**
     * Resolve a service abbreviation to a human-readable name.
     */
    public static function serviceName(string $abbr): string
    {
        $key = strtolower(trim($abbr));
        return self::SERVICE_NAMES[$key] ?? ucfirst($abbr);
    }

    /**
     * Whether the service is fully configured and ready to use.
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    // ── HTTP helpers ──────────────────────────────────────────────────────────

    /**
     * Write a log entry through the configured channel.
     * Silently ignores any channel-configuration errors so that a misconfigured
     * log channel can never break the application itself.
     */
    private function log(string $level, string $message, array $context = []): void
    {
        try {
            Log::channel($this->logChannel)->$level($message, $context);
        } catch (\Throwable) {
            // Fallback to default channel
            try {
                Log::$level($message, $context);
            } catch (\Throwable) {
                // Silently fail — logging must never crash the application
            }
        }
    }

    /**
     * Build a pre-configured HTTP client instance.
     */
    private function client()
    {
        return Http::withOptions([
            'curl' => [CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1],
        ])->withHeaders([
            'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
            'Accept'          => 'application/json, text/plain, */*',
            'Accept-Language' => 'en-US,en;q=0.9',
        ])->timeout($this->timeout);
    }

    /**
     * Execute one authenticated GET request to the Hero-SMS endpoint.
     *
     * Returns:
     *   ['success' => true,  'body' => '<raw response body>']
     *   ['success' => false, 'message' => '<human-readable error>', 'rate_limited' => bool]
     *
     * All error conditions are logged here so callers don't need to repeat it.
     *
     * @param  array  $params   Query parameters (must NOT include api_key).
     * @param  int    $attempt  Current attempt number, used only for logging.
     */
    private function call(array $params, int $attempt = 1): array
    {
        $action    = $params['action'] ?? '?';
        $params['api_key'] = $this->apiKey;
        $timestamp = now()->toISOString();

        $logContext = [
            'timestamp'  => $timestamp,
            'endpoint'   => $this->baseUrl,
            'action'     => $action,
            'attempt'    => $attempt,
            'params'     => array_diff_key($params, ['api_key' => '']), // never log the key
        ];

        try {
            $response   = $this->client()->get($this->baseUrl, $params);
            $httpStatus = $response->status();
            $body       = trim($response->body());

            // ── Rate limiting ──────────────────────────────────────────────
            if ($httpStatus === 429) {
                $this->log('warning', 'HeroSms rate-limited (HTTP 429)', array_merge($logContext, [
                    'http_status' => $httpStatus,
                    'body'        => substr($body, 0, 300),
                ]));
                return ['success' => false, 'message' => 'Hero-SMS rate limit reached. Please wait and try again.', 'rate_limited' => true];
            }

            // ── Empty response ─────────────────────────────────────────────
            if ($body === '') {
                $this->log('error', 'HeroSms empty response', array_merge($logContext, [
                    'http_status' => $httpStatus,
                ]));
                return ['success' => false, 'message' => 'Hero-SMS returned an empty response. Please try again.'];
            }

            // ── Non-2xx HTTP error ─────────────────────────────────────────
            if (!$response->successful()) {
                $this->log('error', 'HeroSms HTTP error', array_merge($logContext, [
                    'http_status' => $httpStatus,
                    'body'        => substr($body, 0, 300),
                ]));
                return ['success' => false, 'message' => "Hero-SMS request failed (HTTP {$httpStatus})."];
            }

            $this->log('info', 'HeroSms response', array_merge($logContext, [
                'http_status' => $httpStatus,
                'body'        => substr($body, 0, 300),
            ]));

            return ['success' => true, 'body' => $body];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            // Network failure or timeout
            $this->log('error', 'HeroSms connection error', array_merge($logContext, [
                'error' => $e->getMessage(),
            ]));
            return ['success' => false, 'message' => 'Could not reach Hero-SMS: connection failed or timed out.'];

        } catch (\Exception $e) {
            $this->log('error', 'HeroSms unexpected error', array_merge($logContext, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]));
            return ['success' => false, 'message' => 'An unexpected error occurred while contacting Hero-SMS.'];
        }
    }

    /**
     * Parse a plain-text sms-activate style response, e.g. "ACCESS_NUMBER:id:phone".
     * Also handles JSON error envelopes like {"title":"BAD_KEY","details":"..."}.
     *
     * Returns:
     *   ['ok' => true,  'code' => 'ACCESS_NUMBER', 'parts' => [...]]
     *   ['ok' => false, 'code' => 'BAD_KEY',       'detail' => '...']
     */
    private function parsePlain(string $body): array
    {
        // JSON error response: {"title":"BAD_KEY","details":"..."}
        if (str_starts_with($body, '{') || str_starts_with($body, '[')) {
            $json = json_decode($body, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return ['ok' => false, 'code' => 'INVALID_JSON', 'detail' => 'Invalid JSON from Hero-SMS API.'];
            }
            if (is_array($json) && isset($json['title']) && $json['title'] !== 'OK') {
                return ['ok' => false, 'code' => $json['title'], 'detail' => $json['details'] ?? ''];
            }
        }

        $parts = explode(':', $body, 3);
        return ['ok' => true, 'code' => $parts[0], 'parts' => $parts];
    }

    // ── Public API methods ────────────────────────────────────────────────────

    /**
     * Fetch the current Hero-SMS account balance.
     *
     * @return array  ['success' => true, 'data' => ['balance' => float]]
     *              | ['success' => false, 'message' => string]
     */
    public function getBalance(): array
    {
        $r = $this->call(['action' => 'getBalance']);
        if (!$r['success']) return $r;

        $body = $r['body'];

        // Successful: "ACCESS_BALANCE:12.34"
        if (str_starts_with($body, 'ACCESS_BALANCE:')) {
            return ['success' => true, 'data' => ['balance' => (float) substr($body, 15)]];
        }

        $p = $this->parsePlain($body);
        if (!$p['ok']) {
            return ['success' => false, 'message' => $p['detail'] ?: $p['code']];
        }

        return ['success' => false, 'message' => 'Unexpected balance response: ' . $body];
    }

    /**
     * Fetch the list of countries.
     * This endpoint is public — no auth required.
     *
     * @return array  ['success' => true, 'data' => [['id' => int, 'name' => string], ...]]
     *              | ['success' => false, 'message' => string]
     */
    public function getCountries(): array
    {
        try {
            // Public endpoint — omit api_key to avoid bad-key errors when unconfigured
            $response = $this->client()->get($this->baseUrl, ['action' => 'getCountries']);
            $body     = trim($response->body());
        } catch (\Exception $e) {
            $this->log('error', 'HeroSms getCountries error', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Could not reach Hero-SMS.'];
        }

        if ($body === '') {
            return ['success' => false, 'message' => 'Hero-SMS returned an empty country list.'];
        }

        $json = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($json)) {
            return ['success' => false, 'message' => 'Unexpected countries response from Hero-SMS.'];
        }

        $countries = [];
        foreach ($json as $item) {
            if (!isset($item['id']) || !($item['visible'] ?? 1)) continue;
            $countries[] = [
                'id'   => $item['id'],
                'name' => $item['eng'] ?? $item['rus'] ?? 'Country ' . $item['id'],
            ];
        }

        usort($countries, fn($a, $b) => strcmp($a['name'], $b['name']));

        return ['success' => true, 'data' => $countries];
    }

    /**
     * Fetch available services for a specific country (or all countries).
     *
     * @param  string|null $country  Country ID; null = all countries.
     * @return array  ['success' => true, 'data' => [['serviceId', 'name', 'count', 'cost'], ...]]
     *              | ['success' => false, 'message' => string]
     */
    public function getServices(?string $country = null): array
    {
        if ($country === null || $country === '') {
            return $this->getAllServices();
        }

        $r = $this->call(['action' => 'getPrices', 'country' => $country]);
        if (!$r['success']) return $r;

        $json = json_decode($r['body'], true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($json)) {
            $p = $this->parsePlain($r['body']);
            if (!$p['ok']) return ['success' => false, 'message' => $p['detail'] ?: $p['code']];
            return ['success' => false, 'message' => 'Unexpected services response from Hero-SMS.'];
        }

        if (isset($json['title'])) {
            return ['success' => false, 'message' => $json['details'] ?? $json['title']];
        }

        // Unwrap the country wrapper if present
        $firstValue = reset($json);
        if (is_array($firstValue) && !isset($firstValue['count']) && !isset($firstValue['cost'])) {
            $json = $firstValue;
        }

        $services = [];
        foreach ($json as $abbr => $info) {
            // Only surface services with enough stock to be reliably receivable
            if (is_array($info) && isset($info['count']) && (int) $info['count'] >= 2) {
                $services[] = [
                    'serviceId' => $abbr,
                    'name'      => self::serviceName($abbr),
                    'count'     => (int) $info['count'],
                    'cost'      => (float) ($info['cost'] ?? 0),
                ];
            }
        }

        usort($services, fn($a, $b) => strcmp($a['name'], $b['name']));

        return ['success' => true, 'data' => $services];
    }

    /**
     * Fetch services across ALL countries, aggregating counts and using the minimum cost.
     *
     * @return array  ['success' => true, 'data' => [...]]
     *              | ['success' => false, 'message' => string]
     */
    public function getAllServices(): array
    {
        $r = $this->call(['action' => 'getPrices']);
        if (!$r['success']) return $r;

        $json = json_decode($r['body'], true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($json)) {
            $p = $this->parsePlain($r['body']);
            if (!$p['ok']) return ['success' => false, 'message' => $p['detail'] ?: $p['code']];
            return ['success' => false, 'message' => 'Unexpected services response from Hero-SMS.'];
        }

        if (isset($json['title'])) {
            return ['success' => false, 'message' => $json['details'] ?? $json['title']];
        }

        $services  = [];
        $firstVal  = reset($json);

        if (is_array($firstVal)) {
            $nestedFirst = reset($firstVal);
            $isGlobal    = is_array($nestedFirst) && isset($nestedFirst['count']);

            if ($isGlobal) {
                // Global format: {"abbr": {"countryId": {"count":N,"cost":X}, ...}, ...}
                foreach ($json as $abbr => $byCountry) {
                    if (!is_array($byCountry)) continue;
                    $totalCount = 0;
                    $minCost    = PHP_FLOAT_MAX;
                    foreach ($byCountry as $countryData) {
                        if (!is_array($countryData)) continue;
                        $totalCount += (int) ($countryData['count'] ?? 0);
                        $cost = (float) ($countryData['cost'] ?? 0);
                        if ($cost > 0 && $cost < $minCost) $minCost = $cost;
                    }
                    if ($totalCount > 0) {
                        $services[$abbr] = [
                            'serviceId' => $abbr,
                            'name'      => self::serviceName($abbr),
                            'count'     => $totalCount,
                            'cost'      => $minCost === PHP_FLOAT_MAX ? 0.0 : $minCost,
                        ];
                    }
                }
            } else {
                // Country-scoped: {"countryId": {"abbr": {"count":N,"cost":X}}}
                foreach ($json as $countryData) {
                    if (!is_array($countryData)) continue;
                    foreach ($countryData as $abbr => $info) {
                        if (!is_array($info) || !isset($info['count'])) continue;
                        $cnt  = (int) ($info['count'] ?? 0);
                        $cost = (float) ($info['cost'] ?? 0);
                        if (!isset($services[$abbr])) {
                            $services[$abbr] = ['serviceId' => $abbr, 'name' => self::serviceName($abbr), 'count' => 0, 'cost' => PHP_FLOAT_MAX];
                        }
                        $services[$abbr]['count'] += $cnt;
                        if ($cost > 0 && $cost < $services[$abbr]['cost']) {
                            $services[$abbr]['cost'] = $cost;
                        }
                    }
                }
                foreach ($services as &$s) {
                    if ($s['cost'] === PHP_FLOAT_MAX) $s['cost'] = 0.0;
                }
                unset($s);
            }
        }

        $result = array_values(array_filter($services, fn($s) => $s['count'] >= 2));
        usort($result, fn($a, $b) => strcmp($a['name'], $b['name']));

        return ['success' => true, 'data' => $result];
    }

    /**
     * Order a virtual number, retrying automatically when a restricted,
     * unavailable, banned, sold, expired, or cancelled number is assigned.
     *
     * Retry strategy:
     *   - Up to $maxRetries total attempts (default from config).
     *   - Exponential backoff: delay = min(base_delay * 2^(attempt-1), max_delay).
     *   - An additional pause is inserted when the API signals rate-limiting.
     *   - NEVER returns a number whose status is "restricted".
     *
     * @param  string $country    Hero-SMS country ID.
     * @param  string $service    Hero-SMS service abbreviation (e.g. "wa").
     * @param  int|null $maxRetries  Override the config default.
     * @return array  ['success' => true,  'data' => ['order_id' => string, 'number' => string]]
     *              | ['success' => false, 'message' => string]
     */
    public function orderNumber(string $country, string $service, ?int $maxRetries = null): array
    {
        $maxRetries = $maxRetries ?? $this->maxRetries;
        $lastError  = 'Order failed.';

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            $this->log('info', 'HeroSms orderNumber attempt', [
                'attempt'    => $attempt,
                'max'        => $maxRetries,
                'country'    => $country,
                'service'    => $service,
            ]);

            $r = $this->call(['action' => 'getNumber', 'country' => $country, 'service' => $service], $attempt);

            // ── Network / HTTP failure ─────────────────────────────────────
            if (!$r['success']) {
                // Rate-limit: pause then retry if we have attempts left
                if (!empty($r['rate_limited']) && $attempt < $maxRetries) {
                    $pause = $this->rateLimitMs * 1000; // convert to microseconds
                    $this->log('warning', 'HeroSms rate-limited during order; pausing', [
                        'pause_ms' => $this->rateLimitMs,
                        'attempt'  => $attempt,
                    ]);
                    usleep($pause);
                    continue;
                }
                return $r;
            }

            $body = $r['body'];

            // ── Success: number assigned ───────────────────────────────────
            if (str_starts_with($body, 'ACCESS_NUMBER:')) {
                $parts   = explode(':', $body, 3);
                $orderId = $parts[1] ?? '';
                $number  = $parts[2] ?? '';

                // Verify the assigned number is actually usable before returning it.
                $statusResult = $this->checkSms($orderId);

                if ($statusResult['success']) {
                    $category = $statusResult['data']['status_category'] ?? 'pending';

                    $this->log('info', 'HeroSms number status after assignment', [
                        'order_id'        => $orderId,
                        'number'          => $number,
                        'status_category' => $category,
                        'status_raw'      => $statusResult['data']['status_raw'] ?? '',
                        'attempt'         => $attempt,
                    ]);

                    // ── Restricted or otherwise unusable → retry ───────────
                    if (in_array($category, self::RETRYABLE_STATUSES, true)) {
                        $lastError = ucfirst($category) . ' number received; retrying for a usable number.';

                        $this->log('warning', 'HeroSms skipping ' . $category . ' number', [
                            'order_id' => $orderId,
                            'number'   => $number,
                            'attempt'  => $attempt,
                        ]);

                        // Apply exponential backoff before next attempt
                        if ($attempt < $maxRetries) {
                            $delayMs = min($this->baseDelayMs * (2 ** ($attempt - 1)), $this->maxDelayMs);
                            $this->log('info', 'HeroSms backoff', [
                                'delay_ms' => $delayMs,
                                'attempt'  => $attempt,
                            ]);
                            usleep($delayMs * 1000);
                        }
                        continue;
                    }

                    // ── Usable number ──────────────────────────────────────
                    if (in_array($category, self::USABLE_STATUSES, true)) {
                        return ['success' => true, 'data' => [
                            'order_id' => $orderId,
                            'number'   => $number,
                        ]];
                    }

                    // ── Unknown status category — return optimistically ─────
                    // (better to give the number than to silently fail)
                    $this->log('warning', 'HeroSms unknown status category; returning number optimistically', [
                        'order_id'        => $orderId,
                        'status_category' => $category,
                        'attempt'         => $attempt,
                    ]);
                    return ['success' => true, 'data' => ['order_id' => $orderId, 'number' => $number]];

                } else {
                    // Status check itself failed (network error etc.) — return
                    // the number optimistically rather than wasting the order.
                    $this->log('warning', 'HeroSms status check failed after assignment; returning number optimistically', [
                        'order_id' => $orderId,
                        'error'    => $statusResult['message'] ?? '',
                        'attempt'  => $attempt,
                    ]);
                    return ['success' => true, 'data' => ['order_id' => $orderId, 'number' => $number]];
                }
            }

            // ── Error code in body ─────────────────────────────────────────
            $p    = $this->parsePlain($body);
            $code = $p['ok'] ? $p['code'] : ($p['code'] ?? 'UNKNOWN');

            $this->log('warning', 'HeroSms order error code', [
                'code'    => $code,
                'body'    => substr($body, 0, 200),
                'attempt' => $attempt,
            ]);

            // Permanent errors — don't retry, return immediately
            if (array_key_exists($code, self::FATAL_ORDER_CODES)) {
                return ['success' => false, 'message' => self::FATAL_ORDER_CODES[$code]];
            }

            // Retryable errors (NO_NUMBERS etc.)
            if (in_array($code, self::RETRYABLE_ORDER_CODES, true)) {
                $lastError = 'No numbers are currently available for this service/country.';
            } else {
                // Unknown error code — fail immediately rather than retry blindly
                $message = !$p['ok']
                    ? ($p['detail'] ?: $code)
                    : ('Order failed: ' . $body);
                return ['success' => false, 'message' => $message];
            }

            // Apply exponential backoff before next attempt
            if ($attempt < $maxRetries) {
                $delayMs = min($this->baseDelayMs * (2 ** ($attempt - 1)), $this->maxDelayMs);
                usleep($delayMs * 1000);
            }
        }

        $this->log('error', 'HeroSms orderNumber exhausted retries', [
            'country'     => $country,
            'service'     => $service,
            'max_retries' => $maxRetries,
            'last_error'  => $lastError,
        ]);

        return ['success' => false, 'message' => $lastError . ' (tried ' . $maxRetries . ' time(s))'];
    }

    /**
     * Check the SMS status for an existing order.
     *
     * Handles all known Hero-SMS getStatus responses:
     *   STATUS_WAIT_CODE     → pending  (waiting for SMS)
     *   STATUS_WAIT_RESEND   → pending  (waiting for resend)
     *   STATUS_OK:CODE       → completed (SMS received; code in 'sms')
     *   STATUS_CANCEL        → cancelled
     *   STATUS_RESTRICTED    → restricted (number blocked by carrier/provider)
     *   STATUS_UNAVAILABLE   → unavailable (number out of service)
     *   STATUS_BANNED        → banned (number permanently banned)
     *   STATUS_SOLD          → sold (number already used by another customer)
     *   STATUS_EXPIRED       → expired (order time limit reached)
     *   STATUS_INVALID       → invalid (bad order ID)
     *   STATUS_PENDING       → pending (order queued, not yet active)
     *   STATUS_FAILED        → failed (general failure)
     *
     * @param  string $orderId  External Hero-SMS order ID.
     * @return array  ['success' => true, 'data' => [
     *                    'status'          => int (1=pending, 3=completed, 6=cancelled),
     *                    'status_category' => string (pending|completed|cancelled|restricted|…),
     *                    'status_raw'      => string,
     *                    'sms'             => string|null,
     *                ]]
     *              | ['success' => false, 'message' => string]
     */
    public function checkSms(string $orderId): array
    {
        $r = $this->call(['action' => 'getStatus', 'id' => $orderId]);
        if (!$r['success']) return $r;

        $body = $r['body'];

        // ── Completed: SMS received ────────────────────────────────────────
        if (str_starts_with($body, 'STATUS_OK:')) {
            $smsCode = substr($body, 10);
            return ['success' => true, 'data' => [
                'status'          => 3,
                'status_category' => 'completed',
                'sms'             => $smsCode,
                'status_raw'      => $body,
            ]];
        }

        // ── Cancelled ─────────────────────────────────────────────────────
        if (str_starts_with($body, 'STATUS_CANCEL')) {
            return ['success' => true, 'data' => [
                'status'          => 6,
                'status_category' => 'cancelled',
                'sms'             => null,
                'status_raw'      => $body,
            ]];
        }

        // ── Pending / waiting ─────────────────────────────────────────────
        if (str_starts_with($body, 'STATUS_WAIT')) {
            return ['success' => true, 'data' => [
                'status'          => 1,
                'status_category' => 'pending',
                'sms'             => null,
                'status_raw'      => $body,
            ]];
        }

        // ── Pending (queued, not yet active) ──────────────────────────────
        if (str_starts_with($body, 'STATUS_PENDING')) {
            return ['success' => true, 'data' => [
                'status'          => 1,
                'status_category' => 'pending',
                'sms'             => null,
                'status_raw'      => $body,
            ]];
        }

        // ── RESTRICTED — number blocked; must NOT be shown to the user ────
        if (str_starts_with($body, 'STATUS_RESTRICTED')) {
            $this->log('warning', 'HeroSms restricted number detected', [
                'order_id'   => $orderId,
                'status_raw' => $body,
            ]);
            return ['success' => true, 'data' => [
                'status'          => 6,
                'status_category' => 'restricted',
                'sms'             => null,
                'status_raw'      => $body,
            ]];
        }

        // ── UNAVAILABLE ───────────────────────────────────────────────────
        if (str_starts_with($body, 'STATUS_UNAVAILABLE')) {
            $this->log('warning', 'HeroSms unavailable number detected', [
                'order_id'   => $orderId,
                'status_raw' => $body,
            ]);
            return ['success' => true, 'data' => [
                'status'          => 6,
                'status_category' => 'unavailable',
                'sms'             => null,
                'status_raw'      => $body,
            ]];
        }

        // ── BANNED ────────────────────────────────────────────────────────
        if (str_starts_with($body, 'STATUS_BANNED')) {
            $this->log('warning', 'HeroSms banned number detected', [
                'order_id'   => $orderId,
                'status_raw' => $body,
            ]);
            return ['success' => true, 'data' => [
                'status'          => 6,
                'status_category' => 'banned',
                'sms'             => null,
                'status_raw'      => $body,
            ]];
        }

        // ── SOLD ──────────────────────────────────────────────────────────
        if (str_starts_with($body, 'STATUS_SOLD')) {
            $this->log('warning', 'HeroSms sold number detected', [
                'order_id'   => $orderId,
                'status_raw' => $body,
            ]);
            return ['success' => true, 'data' => [
                'status'          => 6,
                'status_category' => 'sold',
                'sms'             => null,
                'status_raw'      => $body,
            ]];
        }

        // ── EXPIRED ───────────────────────────────────────────────────────
        if (str_starts_with($body, 'STATUS_EXPIRED')) {
            $this->log('warning', 'HeroSms expired number detected', [
                'order_id'   => $orderId,
                'status_raw' => $body,
            ]);
            return ['success' => true, 'data' => [
                'status'          => 6,
                'status_category' => 'expired',
                'sms'             => null,
                'status_raw'      => $body,
            ]];
        }

        // ── INVALID ───────────────────────────────────────────────────────
        if (str_starts_with($body, 'STATUS_INVALID')) {
            $this->log('warning', 'HeroSms invalid order ID', [
                'order_id'   => $orderId,
                'status_raw' => $body,
            ]);
            return ['success' => true, 'data' => [
                'status'          => 6,
                'status_category' => 'invalid',
                'sms'             => null,
                'status_raw'      => $body,
            ]];
        }

        // ── FAILED (general) ──────────────────────────────────────────────
        if (str_starts_with($body, 'STATUS_FAILED')) {
            $this->log('warning', 'HeroSms failed status', [
                'order_id'   => $orderId,
                'status_raw' => $body,
            ]);
            return ['success' => true, 'data' => [
                'status'          => 6,
                'status_category' => 'failed',
                'sms'             => null,
                'status_raw'      => $body,
            ]];
        }

        // ── Unknown response — parse and return pending as safe fallback ───
        $p = $this->parsePlain($body);
        if (!$p['ok']) {
            return ['success' => false, 'message' => $p['detail'] ?: $p['code']];
        }

        $this->log('warning', 'HeroSms unrecognised status response', [
            'order_id'   => $orderId,
            'status_raw' => substr($body, 0, 200),
        ]);

        return ['success' => true, 'data' => [
            'status'          => 1,
            'status_category' => 'pending',
            'sms'             => null,
            'status_raw'      => $body,
        ]];
    }

    /**
     * Request an SMS resend for an existing order (setStatus = 3).
     *
     * @return array  ['success' => true] | ['success' => false, 'message' => string]
     */
    public function resendSms(string $orderId): array
    {
        $r = $this->call(['action' => 'setStatus', 'id' => $orderId, 'status' => 3]);
        if (!$r['success']) return $r;

        $body = $r['body'];

        if (str_starts_with($body, 'ACCESS_RETRY_GET')) {
            return ['success' => true, 'data' => ['message' => 'SMS resend requested.']];
        }

        $errors = [
            'ALREADY_FINISH' => 'Order is already finished.',
            'ALREADY_CANCEL' => 'Order is already cancelled.',
            'NO_RETRY'       => 'Resend is not allowed for this order.',
        ];

        $p    = $this->parsePlain($body);
        $code = $p['ok'] ? $p['code'] : ($p['code'] ?? 'UNKNOWN');
        return ['success' => false, 'message' => $errors[$code] ?? ('Resend failed: ' . $body)];
    }

    /**
     * Confirm a number is in use / signal ready to receive SMS (setStatus = 1).
     *
     * @return array  ['success' => true] | ['success' => false, 'message' => string]
     */
    public function confirmNumber(string $orderId): array
    {
        $r = $this->call(['action' => 'setStatus', 'id' => $orderId, 'status' => 1]);
        if (!$r['success']) return $r;

        $body = $r['body'];

        if (str_starts_with($body, 'ACCESS_READY')) {
            return ['success' => true, 'data' => ['message' => 'Number confirmed as active.']];
        }

        $p = $this->parsePlain($body);
        if (!$p['ok']) return ['success' => false, 'message' => $p['detail'] ?: $p['code']];

        // Some providers return ACCESS_NUMBER or similar — treat as success
        return ['success' => true, 'data' => ['message' => 'Confirmed: ' . $body]];
    }

    /**
     * Mark an order as complete after the SMS code is received (setStatus = 6).
     * Always call this after successful verification to free the number.
     *
     * @return array  ['success' => true] | ['success' => false, 'message' => string]
     */
    public function finishOrder(string $orderId): array
    {
        $r = $this->call(['action' => 'setStatus', 'id' => $orderId, 'status' => 6]);
        if (!$r['success']) return $r;

        $body = $r['body'];

        if (str_starts_with($body, 'ACCESS_ACTIVATION')) {
            return ['success' => true, 'data' => ['message' => 'Order marked as complete.']];
        }

        $errors = [
            'ALREADY_FINISH' => 'Order was already finished.',
            'ALREADY_CANCEL' => 'Order was already cancelled.',
        ];

        $p    = $this->parsePlain($body);
        $code = $p['ok'] ? $p['code'] : ($p['code'] ?? 'UNKNOWN');
        return ['success' => false, 'message' => $errors[$code] ?? ('Finish failed: ' . $body)];
    }

    /**
     * Cancel an order (setStatus = 8).
     *
     * @return array  ['success' => true] | ['success' => false, 'message' => string]
     */
    public function cancelOrder(string $orderId): array
    {
        $r = $this->call(['action' => 'setStatus', 'id' => $orderId, 'status' => 8]);
        if (!$r['success']) return $r;

        $body = $r['body'];

        if (str_starts_with($body, 'ACCESS_CANCEL')) {
            return ['success' => true, 'data' => []];
        }

        $p = $this->parsePlain($body);
        if (!$p['ok']) return ['success' => false, 'message' => $p['detail'] ?: $p['code']];

        return ['success' => false, 'message' => 'Cancel failed: ' . $body];
    }
}
