<?php
namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Hero-SMS API (sms-activate compatible style)
 * Endpoint: https://hero-sms.com/stubs/handler_api.php
 */
class HeroSmsService
{
    private string $apiKey;
    private string $baseUrl = 'https://hero-sms.com/stubs/handler_api.php';

    private const SERVICE_NAMES = [
        'ab'  => 'Airbnb',
        'am'  => 'Amazon',
        'av'  => 'Avito',
        'ay'  => 'AliExpress',
        'az'  => 'Amazon',
        'ba'  => 'Badoo',
        'bb'  => 'Blizzard',
        'bd'  => 'BandLab',
        'bi'  => 'Binance',
        'bk'  => 'Booking',
        'bn'  => 'Binance',
        'bo'  => 'Bolt',
        'bt'  => 'BitCoin',
        'bu'  => 'Bumble',
        'bz'  => 'Bizznes',
        'ca'  => 'Careem',
        'cb'  => 'Coinbase',
        'cc'  => 'Cash App',
        'cf'  => 'Cloudflare',
        'ci'  => 'Cian',
        'ck'  => 'ClickUp',
        'cl'  => 'Craigslist',
        'cm'  => 'Clubmoss',
        'cn'  => 'Canva',
        'cp'  => 'Crypto.com',
        'cs'  => 'Snapchat',
        'dc'  => 'DoorDash',
        'dl'  => 'Deliveroo',
        'dm'  => 'DMarket',
        'dn'  => 'Deezer',
        'ds'  => 'Discord',
        'dt'  => 'Dating',
        'du'  => 'Dubsmash',
        'dz'  => 'Dzen',
        'ea'  => 'EA Sports',
        'eb'  => 'eBay',
        'ep'  => 'eBay',
        'et'  => 'Etsy',
        'ex'  => 'Exmo',
        'ez'  => 'Ezzocard',
        'fb'  => 'Facebook',
        'fc'  => 'FoodClub',
        'fd'  => 'Fiverr',
        'fi'  => 'Fiverr',
        'fk'  => 'Freelancer',
        'fl'  => 'Freelancer',
        'fm'  => 'FM Radio',
        'fo'  => 'Foot Locker',
        'ft'  => 'Fotostrana',
        'fv'  => 'Fiver',
        'ga'  => 'Google Ads',
        'gb'  => 'Grab',
        'gf'  => 'GreenFarm',
        'gg'  => 'GaGa',
        'gh'  => 'GitHub',
        'gi'  => 'Grindr',
        'gk'  => 'Gekko',
        'gl'  => 'GitLab',
        'gm'  => 'Gmail',
        'gn'  => 'GreenMan',
        'go'  => 'Google',
        'gp'  => 'Google Pay',
        'gr'  => 'Groupon',
        'gs'  => 'Google Services',
        'gt'  => 'GetApp',
        'gu'  => 'Guru',
        'gv'  => 'Google Voice',
        'gy'  => 'Grubhub',
        'hb'  => 'Habr',
        'hh'  => 'HeadHunter',
        'hm'  => 'H&M',
        'hn'  => 'Honey',
        'hp'  => 'HotPot',
        'ht'  => 'Hitch',
        'hv'  => 'Hive',
        'hw'  => 'Huawei',
        'hy'  => 'Hyundai',
        'ia'  => 'Instagram Ads',
        'ic'  => 'iCloud',
        'ig'  => 'Instagram',
        'im'  => 'iMessage',
        'in'  => 'Indeed',
        'iq'  => 'IQOption',
        'is'  => 'iShot',
        'it'  => 'iTunes',
        'jk'  => 'Jike',
        'jm'  => 'Jumia',
        'kb'  => 'KuBit',
        'kc'  => 'KuCoin',
        'kk'  => 'Kakao',
        'kr'  => 'Kraken',
        'kt'  => 'Krait',
        'ku'  => 'Kuaishou',
        'kw'  => 'KiwiWallet',
        'ky'  => 'KikMessenger',
        'la'  => 'Lazada',
        'lb'  => 'LeBonCoin',
        'lc'  => 'LuckyCash',
        'ld'  => 'LinkedIn',
        'lf'  => 'Lyft',
        'li'  => 'Line',
        'lk'  => 'LinkedIn',
        'lm'  => 'Lemon',
        'ln'  => 'LinkedIn',
        'lo'  => 'Lookout',
        'lr'  => 'Lazr',
        'ls'  => 'Lalafo',
        'lt'  => 'Letgo',
        'lv'  => 'Lovoo',
        'lw'  => 'Lawgical',
        'ma'  => 'Mail.ru',
        'mb'  => 'MobiBase',
        'mc'  => 'Microsoft',
        'md'  => 'Mailchimp',
        'me'  => 'Mercado',
        'mf'  => 'MobFox',
        'mg'  => 'MegaFon',
        'mh'  => 'Mashup',
        'mi'  => 'Mi (Xiaomi)',
        'mk'  => 'Market',
        'ml'  => 'Mail.ru',
        'mm'  => 'Microsoft',
        'mn'  => 'Monese',
        'mo'  => 'Momo',
        'mp'  => 'Moped',
        'mr'  => 'Miro',
        'ms'  => 'Microsoft',
        'mt'  => 'Metaco',
        'mu'  => 'MutualFund',
        'mv'  => 'Movistar',
        'mx'  => 'Mercado',
        'my'  => 'MyLead',
        'mz'  => 'Meizu',
        'na'  => 'Napster',
        'nb'  => 'Northbank',
        'nc'  => 'NordVPN',
        'nd'  => 'Nintendo',
        'nf'  => 'Netflix',
        'ni'  => 'Nike',
        'nk'  => 'Nokia',
        'nl'  => 'NordLayer',
        'nm'  => 'Nium',
        'no'  => 'Notion',
        'np'  => 'Napier',
        'ns'  => 'Nintendo Switch',
        'nt'  => 'Neteller',
        'nu'  => 'Nubank',
        'nv'  => 'Nvidia',
        'nw'  => 'NordWallet',
        'nx'  => 'NordX',
        'ny'  => 'Nayax',
        'oa'  => 'OkadaAfrica',
        'ob'  => 'OB Accounts',
        'oc'  => 'OctaFX',
        'od'  => 'Odnoklassniki',
        'oe'  => 'OFX',
        'of'  => 'OnlyFans',
        'og'  => 'OG',
        'oh'  => 'Ohm',
        'oi'  => 'OI',
        'ok'  => 'Odnoklassniki',
        'ol'  => 'Olx',
        'om'  => 'OLX',
        'on'  => 'Ona',
        'op'  => 'OpenAI',
        'or'  => 'Orange',
        'os'  => 'OsamuShip',
        'ot'  => 'Other',
        'ou'  => 'Outrider',
        'ov'  => 'OVH',
        'ow'  => 'Owlet',
        'ox'  => 'OX',
        'oy'  => 'Oyster',
        'pa'  => 'PayPal',
        'pb'  => 'Paytm',
        'pc'  => 'PocketCard',
        'pd'  => 'Pandora',
        'pe'  => 'Perpay',
        'pf'  => 'Pocketful',
        'pg'  => 'PayGo',
        'ph'  => 'Phemex',
        'pi'  => 'Pinterest',
        'pj'  => 'PJ',
        'pk'  => 'PocketKnife',
        'pl'  => 'PolyAI',
        'pm'  => 'ProtonMail',
        'pn'  => 'PineLabs',
        'po'  => 'Poshmark',
        'pp'  => 'PayPal',
        'pr'  => 'Proton',
        'ps'  => 'PlayStation',
        'pt'  => 'Pinterest',
        'pu'  => 'Pumu',
        'pv'  => 'Paysafecard',
        'pw'  => 'Powerbank',
        'px'  => 'PaxFul',
        'py'  => 'Paytm',
        'qa'  => 'QA',
        'qr'  => 'QR',
        'rd'  => 'Reddit',
        'ri'  => 'Riya',
        'rk'  => 'Rakuten',
        'rl'  => 'Revolut',
        'rm'  => 'Rummy',
        'ro'  => 'Robinhood',
        'rp'  => 'Rapid',
        'rs'  => 'Resy',
        'rt'  => 'Rutube',
        'ru'  => 'RuStore',
        'rv'  => 'Revolut',
        'rz'  => 'Razorpay',
        'sa'  => 'Samsung',
        'sb'  => 'Shopee',
        'sc'  => 'Snapchat',
        'sd'  => 'Shopify',
        'se'  => 'SendBird',
        'sf'  => 'Surfshark',
        'sg'  => 'Signal',
        'sh'  => 'Shopee',
        'si'  => 'Signal',
        'sk'  => 'Skype',
        'sl'  => 'Slack',
        'sm'  => 'SMS',
        'sn'  => 'Sina',
        'so'  => 'Shopify',
        'sp'  => 'Spotify',
        'sq'  => 'Square',
        'sr'  => 'Stripe',
        'ss'  => 'Samsung',
        'st'  => 'Steam',
        'su'  => 'Substack',
        'sv'  => 'Skrill',
        'sw'  => 'Sweatcoin',
        'sx'  => 'SX',
        'sy'  => 'Sympla',
        'sz'  => 'Shazam',
        'ta'  => 'Taobao',
        'tb'  => 'Tubi',
        'tc'  => 'TrueCaller',
        'td'  => 'TikTok Shop',
        'te'  => 'Telegram',
        'tf'  => 'TrueFoundry',
        'tg'  => 'Telegram',
        'th'  => 'Thorn',
        'ti'  => 'Tinder',
        'tj'  => 'TJ',
        'tk'  => 'TikTok',
        'tl'  => 'Talabat',
        'tm'  => 'Twitch',
        'tn'  => 'Tantan',
        'to'  => 'Tokopedia',
        'tp'  => 'Tapatalk',
        'tq'  => 'TQ',
        'tr'  => 'Twitter / X',
        'ts'  => 'TextShark',
        'tt'  => 'TikTok',
        'tu'  => 'Tumblr',
        'tv'  => 'Twitch',
        'tw'  => 'Twitter / X',
        'tx'  => 'TextNow',
        'ty'  => 'ToyCity',
        'tz'  => 'Tazz',
        'ub'  => 'Uber',
        'uc'  => 'UCWeb',
        'ud'  => 'Udemy',
        'ue'  => 'UberEats',
        'uf'  => 'UFO',
        'ug'  => 'Upwork',
        'uh'  => 'UHealth',
        'ui'  => 'UI',
        'uk'  => 'Uklon',
        'ul'  => 'Ulmart',
        'um'  => 'UM',
        'un'  => 'Unnamed',
        'uo'  => 'Uolo',
        'up'  => 'Upwork',
        'uq'  => 'UQ',
        'ur'  => 'Urban',
        'us'  => 'US',
        'ut'  => 'Utair',
        'uu'  => 'UU',
        'uv'  => 'UV',
        'uw'  => 'UW',
        'ux'  => 'UX',
        'uy'  => 'UY',
        'uz'  => 'UZ',
        'vb'  => 'Viber',
        'vc'  => 'VKontakte',
        'vg'  => 'VG',
        'vi'  => 'Viber',
        'vk'  => 'VKontakte',
        'vl'  => 'VL',
        'vm'  => 'Vimeo',
        'vn'  => 'VN',
        'vo'  => 'Vocalink',
        'vp'  => 'VPN',
        'vr'  => 'VR',
        'vs'  => 'VS',
        'vt'  => 'VT',
        'vu'  => 'VU',
        'vv'  => 'VV',
        'vw'  => 'Volkswagen',
        'vx'  => 'VX',
        'vy'  => 'VY',
        'vz'  => 'VZ',
        'wa'  => 'WhatsApp',
        'wb'  => 'WeBank',
        'wc'  => 'WeChat',
        'wd'  => 'WD',
        'we'  => 'WeChat',
        'wf'  => 'WF',
        'wg'  => 'WG',
        'wh'  => 'WH',
        'wi'  => 'Wildberries',
        'wj'  => 'WJ',
        'wk'  => 'WK',
        'wl'  => 'WL',
        'wm'  => 'WM',
        'wn'  => 'WN',
        'wo'  => 'WO',
        'wp'  => 'WordPress',
        'wq'  => 'WQ',
        'wr'  => 'WR',
        'ws'  => 'WS',
        'wt'  => 'WhatsApp Business',
        'wu'  => 'WU',
        'wv'  => 'WV',
        'ww'  => 'WW',
        'wx'  => 'WX',
        'wy'  => 'WY',
        'wz'  => 'WZ',
        'xa'  => 'Xiaomi',
        'xb'  => 'XBox',
        'xi'  => 'Xiaomi',
        'xm'  => 'XM',
        'ya'  => 'Yandex',
        'yk'  => 'Yandex',
        'ym'  => 'YandexMoney',
        'yo'  => 'YouTube',
        'yt'  => 'YouTube',
        'yu'  => 'YU',
        'yy'  => 'YY',
        'za'  => 'Zalo',
        'zl'  => 'Zalo',
        'zo'  => 'Zoom',
        'zp'  => 'ZaloPay',
        'zt'  => 'ZT',
    ];

    public static function serviceName(string $abbr): string
    {
        $key = strtolower(trim($abbr));
        return self::SERVICE_NAMES[$key] ?? ucfirst($abbr);
    }

    public function __construct()
    {
        $this->apiKey = trim(Setting::get('herosms_api_key', ''));
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    // ── HTTP helpers ───────────────────────────────────────────────────────────

    private function client()
    {
        return Http::withOptions([
            'curl' => [CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1],
        ])->withHeaders([
            'User-Agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
            'Accept'          => 'application/json, text/plain, */*',
            'Accept-Language' => 'en-US,en;q=0.9',
        ])->timeout(20);
    }

    private function call(array $params): array
    {
        $params['api_key'] = $this->apiKey;
        try {
            $response = $this->client()->get($this->baseUrl, $params);
            $status   = $response->status();
            $body     = trim($response->body());

            Log::info('HeroSms [' . ($params['action'] ?? '?') . '] HTTP ' . $status . ' | ' . substr($body, 0, 300));

            if (!$response->successful()) {
                return ['success' => false, 'message' => 'Hero-SMS request failed (HTTP ' . $status . ').'];
            }

            return ['success' => true, 'body' => $body];
        } catch (\Exception $e) {
            Log::error('HeroSmsService error [' . ($params['action'] ?? '?') . ']: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Could not reach Hero-SMS. Check your connection.'];
        }
    }

    /** Parse a plain-text sms-activate style response, e.g. ACCESS_NUMBER:id:phone */
    private function parsePlain(string $body): array
    {
        // JSON error response: {"title":"BAD_KEY","details":"..."}
        if (str_starts_with($body, '{') || str_starts_with($body, '[')) {
            $json = json_decode($body, true);
            if (is_array($json)) {
                if (isset($json['title']) && $json['title'] !== 'OK') {
                    return ['ok' => false, 'code' => $json['title'], 'detail' => $json['details'] ?? ''];
                }
            }
        }

        $parts = explode(':', $body, 3);
        return ['ok' => true, 'code' => $parts[0], 'parts' => $parts];
    }

    // ── Public API methods ─────────────────────────────────────────────────────

    /** Returns ['success'=>true,'data'=>['balance'=>X.XX]] */
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
     * Returns list of countries as array of ['id'=>N, 'name'=>'Country'].
     * This endpoint is public — no auth required.
     */
    public function getCountries(): array
    {
        // Public endpoint — omit api_key to avoid bad-key errors on unconfigured keys
        try {
            $response = $this->client()->get($this->baseUrl, ['action' => 'getCountries']);
            $body = trim($response->body());
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Could not reach Hero-SMS.'];
        }

        $json = json_decode($body, true);
        if (!is_array($json)) {
            return ['success' => false, 'message' => 'Unexpected countries response.'];
        }

        // Convert {"1":{"id":1,"eng":"Ukraine",...},...} → flat array
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
     * Returns services for a specific country.
     * Response: {"countryId": {"service": {"count":N,"cost":X.XX}, ...}}
     */
    public function getServices(?string $country = null): array
    {
        if ($country === null || $country === '') {
            return $this->getAllServices();
        }

        $r = $this->call(['action' => 'getPrices', 'country' => $country]);
        if (!$r['success']) return $r;

        $json = json_decode($r['body'], true);
        if (!is_array($json)) {
            $p = $this->parsePlain($r['body']);
            if (!$p['ok']) return ['success' => false, 'message' => $p['detail'] ?: $p['code']];
            return ['success' => false, 'message' => 'Unexpected services response.'];
        }

        if (isset($json['title'])) {
            return ['success' => false, 'message' => $json['details'] ?? $json['title']];
        }

        // Response is {"countryId": {"service_abbr": {"count":N,"cost":X}, ...}}
        // Unwrap the country wrapper if present
        $firstValue = reset($json);
        if (is_array($firstValue) && !isset($firstValue['count']) && !isset($firstValue['cost'])) {
            $json = $firstValue;
        }

        // Minimum stock threshold — only surface services with enough numbers to be reliably receivable
        $minCount = 2;

        $services = [];
        foreach ($json as $abbr => $info) {
            if (is_array($info) && isset($info['count']) && (int)($info['count']) >= $minCount) {
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
     * Fetch ALL services across all countries, aggregating counts and using the min cost.
     * Global response format: {"service_abbr": {"countryId": {"count":N,"cost":X}, ...}, ...}
     */
    public function getAllServices(): array
    {
        $r = $this->call(['action' => 'getPrices']);
        if (!$r['success']) return $r;

        $json = json_decode($r['body'], true);
        if (!is_array($json)) {
            $p = $this->parsePlain($r['body']);
            if (!$p['ok']) return ['success' => false, 'message' => $p['detail'] ?: $p['code']];
            return ['success' => false, 'message' => 'Unexpected services response.'];
        }

        if (isset($json['title'])) {
            return ['success' => false, 'message' => $json['details'] ?? $json['title']];
        }

        // Detect format: global = {"abbr": {"countryId": {count,cost}}}
        // country-scoped = {"countryId": {"abbr": {count,cost}}}
        // We check if the first nested value has numeric string keys (country IDs)
        $services = [];
        $firstVal = reset($json);

        if (is_array($firstVal)) {
            $nestedFirst = reset($firstVal);
            $isGlobal = is_array($nestedFirst) && isset($nestedFirst['count']);

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
            }
        }

        // Only return services with enough stock to be reliably receivable
        $result = array_values(array_filter($services, fn($s) => $s['count'] >= 2));
        usort($result, fn($a, $b) => strcmp($a['name'], $b['name']));

        return ['success' => true, 'data' => $result];
    }

    /**
     * Order a number, with automatic retry on NO_NUMBERS (up to $maxRetries attempts).
     * Only returns a number confirmed as waiting for SMS (status=1 / ACCESS_NUMBER response).
     * Returns ['success'=>true,'data'=>['order_id'=>'...','number'=>'...']]
     */
    public function orderNumber(string $country, string $service, int $maxRetries = 3): array
    {
        $errors = [
            'NO_NUMBERS'  => 'No numbers available for this service/country.',
            'NO_BALANCE'  => 'Insufficient Hero-SMS account balance.',
            'BAD_SERVICE' => 'Invalid service selected.',
            'BAD_COUNTRY' => 'Invalid country selected.',
            'BAD_KEY'     => 'Hero-SMS API key is invalid.',
        ];

        $lastError = 'Order failed.';

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            $r = $this->call([
                'action'  => 'getNumber',
                'country' => $country,
                'service' => $service,
            ]);

            if (!$r['success']) return $r;

            $body = $r['body'];

            // Success: "ACCESS_NUMBER:12345:79001234567"
            if (str_starts_with($body, 'ACCESS_NUMBER:')) {
                $parts = explode(':', $body, 3);
                $orderId = $parts[1] ?? '';
                $number  = $parts[2] ?? '';

                // Verify the number is actually waiting for SMS before returning it
                $statusCheck = $this->checkSms($orderId);
                if ($statusCheck['success'] && isset($statusCheck['data']['status'])) {
                    $status = $statusCheck['data']['status'];
                    // status 1 = waiting for code — this is a working, active number
                    if ($status === 1) {
                        return ['success' => true, 'data' => [
                            'order_id' => $orderId,
                            'number'   => $number,
                        ]];
                    }
                    // status 6 = already cancelled — retry
                    if ($status === 6) {
                        $lastError = 'Number was cancelled immediately. Retrying...';
                        usleep(500000); // 0.5s pause before retry
                        continue;
                    }
                }

                // Status check failed or unexpected — still return the number optimistically
                return ['success' => true, 'data' => [
                    'order_id' => $orderId,
                    'number'   => $number,
                ]];
            }

            $p    = $this->parsePlain($body);
            $code = $p['ok'] ? $p['code'] : ($p['code'] ?? 'UNKNOWN');

            // Only retry on NO_NUMBERS; all other errors are permanent
            if ($code !== 'NO_NUMBERS') {
                $message = !$p['ok'] ? ($p['detail'] ?: ($errors[$code] ?? $code))
                                     : ($errors[$code] ?? ('Order failed: ' . $body));
                return ['success' => false, 'message' => $message];
            }

            $lastError = $errors['NO_NUMBERS'];

            if ($attempt < $maxRetries) {
                usleep(700000); // 0.7s pause between retries
            }
        }

        return ['success' => false, 'message' => $lastError . ' (tried ' . $maxRetries . ' times)'];
    }

    /**
     * Request a new SMS to be sent to the number (useful if SMS is delayed).
     * Sends setStatus with status=3 — "request resend".
     * Returns ['success'=>true] on success.
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

        $p = $this->parsePlain($body);
        $code = $p['ok'] ? $p['code'] : ($p['code'] ?? 'UNKNOWN');
        return ['success' => false, 'message' => $errors[$code] ?? ('Resend failed: ' . $body)];
    }

    /**
     * Confirm a number is ready / mark it as active (setStatus with status=1).
     * Call this after ordering to signal the number is in use.
     * Returns ['success'=>true] on success.
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

        // Some providers return ACCESS_NUMBER or similar — treat as ok
        return ['success' => true, 'data' => ['message' => 'Confirmed: ' . $body]];
    }

    /**
     * Mark an order as complete after successfully receiving the SMS code.
     * Sends setStatus with status=6 — "finish".
     * Always call this after a successful verification to free the number.
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
     * Check SMS status for an order.
     * Returns ['success'=>true,'data'=>['status_raw'=>'...','sms'=>'...','status'=>1|3|6]]
     */
    public function checkSms(string $orderId): array
    {
        $r = $this->call(['action' => 'getStatus', 'id' => $orderId]);
        if (!$r['success']) return $r;

        $body = $r['body'];

        // STATUS_WAIT_CODE         → pending (1)
        // STATUS_WAIT_RESEND       → pending (1)
        // STATUS_WAIT_CODE:X       → pending (1)
        // STATUS_OK:CODE123        → completed (3)
        // STATUS_CANCEL            → cancelled (6)

        if (str_starts_with($body, 'STATUS_OK:')) {
            $code = substr($body, 10);
            return ['success' => true, 'data' => ['status' => 3, 'sms' => $code, 'status_raw' => $body]];
        }

        if (str_starts_with($body, 'STATUS_CANCEL')) {
            return ['success' => true, 'data' => ['status' => 6, 'sms' => null, 'status_raw' => $body]];
        }

        if (str_starts_with($body, 'STATUS_WAIT')) {
            return ['success' => true, 'data' => ['status' => 1, 'sms' => null, 'status_raw' => $body]];
        }

        $p = $this->parsePlain($body);
        if (!$p['ok']) return ['success' => false, 'message' => $p['detail'] ?: $p['code']];

        return ['success' => true, 'data' => ['status' => 1, 'sms' => null, 'status_raw' => $body]];
    }

    /**
     * Cancel an order (setStatus with status=8).
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
