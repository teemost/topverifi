<?php
namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * GrizzlySMS API Integration
 * Docs: https://grizzlysms.com/docs
 * Base: https://api.grizzlysms.com/stubs/handler_api.php
 * All requests are HTTP GET; responses are plain-text strings (not JSON).
 */
class GrizzlySmsService
{
    private string $apiKey;
    private string $baseUrl = 'https://api.grizzlysms.com/stubs/handler_api.php';
    private float  $usdToNgn;

    // Numeric country codes (SMS-Activate / GrizzlySMS compatible)
    private const COUNTRIES = [
        ['code' => '12',  'name' => 'USA',              'iso' => 'us'],
        ['code' => '22',  'name' => 'India',             'iso' => 'in'],
        ['code' => '73',  'name' => 'Brazil',            'iso' => 'br'],
        ['code' => '36',  'name' => 'Canada',            'iso' => 'ca'],
        ['code' => '16',  'name' => 'United Kingdom',    'iso' => 'gb'],
        ['code' => '43',  'name' => 'Germany',           'iso' => 'de'],
        ['code' => '78',  'name' => 'France',            'iso' => 'fr'],
        ['code' => '86',  'name' => 'Italy',             'iso' => 'it'],
        ['code' => '56',  'name' => 'Spain',             'iso' => 'es'],
        ['code' => '48',  'name' => 'Netherlands',       'iso' => 'nl'],
        ['code' => '46',  'name' => 'Sweden',            'iso' => 'se'],
        ['code' => '15',  'name' => 'Poland',            'iso' => 'pl'],
        ['code' => '82',  'name' => 'Belgium',           'iso' => 'be'],
        ['code' => '50',  'name' => 'Austria',           'iso' => 'at'],
        ['code' => '63',  'name' => 'Czech Republic',    'iso' => 'cz'],
        ['code' => '32',  'name' => 'Romania',           'iso' => 'ro'],
        ['code' => '83',  'name' => 'Bulgaria',          'iso' => 'bg'],
        ['code' => '84',  'name' => 'Hungary',           'iso' => 'hu'],
        ['code' => '23',  'name' => 'Ireland',           'iso' => 'ie'],
        ['code' => '0',   'name' => 'Russia',            'iso' => 'ru'],
        ['code' => '1',   'name' => 'Ukraine',           'iso' => 'ua'],
        ['code' => '51',  'name' => 'Belarus',           'iso' => 'by'],
        ['code' => '2',   'name' => 'Kazakhstan',        'iso' => 'kz'],
        ['code' => '40',  'name' => 'Uzbekistan',        'iso' => 'uz'],
        ['code' => '11',  'name' => 'Kyrgyzstan',        'iso' => 'kg'],
        ['code' => '62',  'name' => 'Turkey',            'iso' => 'tr'],
        ['code' => '6',   'name' => 'Indonesia',         'iso' => 'id'],
        ['code' => '4',   'name' => 'Philippines',       'iso' => 'ph'],
        ['code' => '10',  'name' => 'Vietnam',           'iso' => 'vn'],
        ['code' => '7',   'name' => 'Malaysia',          'iso' => 'my'],
        ['code' => '52',  'name' => 'Thailand',          'iso' => 'th'],
        ['code' => '3',   'name' => 'China',             'iso' => 'cn'],
        ['code' => '14',  'name' => 'Hong Kong',         'iso' => 'hk'],
        ['code' => '55',  'name' => 'Taiwan',            'iso' => 'tw'],
        ['code' => '60',  'name' => 'Bangladesh',        'iso' => 'bd'],
        ['code' => '66',  'name' => 'Pakistan',          'iso' => 'pk'],
        ['code' => '81',  'name' => 'Nepal',             'iso' => 'np'],
        ['code' => '64',  'name' => 'Sri Lanka',         'iso' => 'lk'],
        ['code' => '5',   'name' => 'Myanmar',           'iso' => 'mm'],
        ['code' => '24',  'name' => 'Cambodia',          'iso' => 'kh'],
        ['code' => '72',  'name' => 'Mongolia',          'iso' => 'mn'],
        ['code' => '53',  'name' => 'Saudi Arabia',      'iso' => 'sa'],
        ['code' => '94',  'name' => 'UAE',               'iso' => 'ae'],
        ['code' => '47',  'name' => 'Iraq',              'iso' => 'iq'],
        ['code' => '13',  'name' => 'Israel',            'iso' => 'il'],
        ['code' => '57',  'name' => 'Iran',              'iso' => 'ir'],
        ['code' => '21',  'name' => 'Egypt',             'iso' => 'eg'],
        ['code' => '19',  'name' => 'Nigeria',           'iso' => 'ng'],
        ['code' => '38',  'name' => 'Ghana',             'iso' => 'gh'],
        ['code' => '8',   'name' => 'Kenya',             'iso' => 'ke'],
        ['code' => '31',  'name' => 'South Africa',      'iso' => 'za'],
        ['code' => '37',  'name' => 'Morocco',           'iso' => 'ma'],
        ['code' => '41',  'name' => 'Cameroon',          'iso' => 'cm'],
        ['code' => '75',  'name' => 'Uganda',            'iso' => 'ug'],
        ['code' => '9',   'name' => 'Tanzania',          'iso' => 'tz'],
        ['code' => '58',  'name' => 'Algeria',           'iso' => 'dz'],
        ['code' => '89',  'name' => 'Tunisia',           'iso' => 'tn'],
        ['code' => '61',  'name' => 'Senegal',           'iso' => 'sn'],
        ['code' => '69',  'name' => 'Mali',              'iso' => 'ml'],
        ['code' => '39',  'name' => 'Argentina',         'iso' => 'ar'],
        ['code' => '54',  'name' => 'Mexico',            'iso' => 'mx'],
        ['code' => '33',  'name' => 'Colombia',          'iso' => 'co'],
        ['code' => '65',  'name' => 'Peru',              'iso' => 'pe'],
        ['code' => '91',  'name' => 'Bolivia',           'iso' => 'bo'],
        ['code' => '70',  'name' => 'Venezuela',         'iso' => 've'],
        ['code' => '34',  'name' => 'Ethiopia',          'iso' => 'et'],
        ['code' => '29',  'name' => 'Azerbaijan',        'iso' => 'az'],
        ['code' => '26',  'name' => 'Lithuania',         'iso' => 'lt'],
        ['code' => '45',  'name' => 'Slovakia',          'iso' => 'sk'],
        ['code' => '44',  'name' => 'Serbia',            'iso' => 'rs'],
        ['code' => '35',  'name' => 'Moldova',           'iso' => 'md'],
        ['code' => '67',  'name' => 'Finland',           'iso' => 'fi'],
        ['code' => '27',  'name' => 'Latvia',            'iso' => 'lv'],
        ['code' => '28',  'name' => 'Estonia',           'iso' => 'ee'],
        ['code' => '30',  'name' => 'Georgia',           'iso' => 'ge'],
        ['code' => '17',  'name' => 'Hong Kong',         'iso' => 'hk'],
        ['code' => '85',  'name' => 'Croatia',           'iso' => 'hr'],
        ['code' => '88',  'name' => 'Norway',            'iso' => 'no'],
        ['code' => '74',  'name' => 'Portugal',          'iso' => 'pt'],
        ['code' => '76',  'name' => 'Greece',            'iso' => 'gr'],
        ['code' => '77',  'name' => 'Denmark',           'iso' => 'dk'],
        ['code' => '79',  'name' => 'Switzerland',       'iso' => 'ch'],
        ['code' => '80',  'name' => 'Jordan',            'iso' => 'jo'],
        ['code' => '87',  'name' => 'Kuwait',            'iso' => 'kw'],
        ['code' => '90',  'name' => 'Sudan',             'iso' => 'sd'],
        ['code' => '92',  'name' => 'Laos',              'iso' => 'la'],
        ['code' => '93',  'name' => 'Haiti',             'iso' => 'ht'],
        ['code' => '95',  'name' => 'Angola',            'iso' => 'ao'],
        ['code' => '96',  'name' => 'Chile',             'iso' => 'cl'],
        ['code' => '97',  'name' => 'Guatemala',         'iso' => 'gt'],
        ['code' => '98',  'name' => 'Ivory Coast',       'iso' => 'ci'],
        ['code' => '99',  'name' => 'Zimbabwe',          'iso' => 'zw'],
        ['code' => '100', 'name' => 'Paraguay',          'iso' => 'py'],
        ['code' => '101', 'name' => 'Uruguay',           'iso' => 'uy'],
        ['code' => '102', 'name' => 'Panama',            'iso' => 'pa'],
        ['code' => '103', 'name' => 'Cuba',              'iso' => 'cu'],
        ['code' => '104', 'name' => 'DR Congo',          'iso' => 'cd'],
        ['code' => '105', 'name' => 'Honduras',          'iso' => 'hn'],
        ['code' => '106', 'name' => 'Rwanda',            'iso' => 'rw'],
        ['code' => '107', 'name' => 'Mozambique',        'iso' => 'mz'],
        ['code' => '108', 'name' => 'Zambia',            'iso' => 'zm'],
    ];

    // Comprehensive service codes → human-readable display names
    // Compatible with SMS-Activate / GrizzlySMS API codes
    private const SERVICE_NAMES = [
        // ── Social & Messaging ────────────────────────────────────────
        'tg'   => 'Telegram',
        'wa'   => 'WhatsApp',
        'wv'   => 'WhatsApp Business',
        'wb'   => 'WhatsApp',
        'wp'   => 'WhatsApp',
        'fb'   => 'Facebook',
        'tw'   => 'Twitter / X',
        'ig'   => 'Instagram',
        'tk'   => 'TikTok',
        'vi'   => 'Viber',
        'we'   => 'WeChat',
        'sn'   => 'Snapchat',
        'dr'   => 'Discord',
        'li'   => 'Line',
        'im'   => 'IMO',
        'sg'   => 'Signal',
        'me'   => 'MeWe',
        'sk'   => 'Skype',
        'ok'   => 'Odnoklassniki',
        'vk'   => 'VKontakte',
        'tb'   => 'Twitch',
        'rd'   => 'Reddit',
        'pi'   => 'Pinterest',
        'tu'   => 'Tumblr',
        'cl'   => 'Clubhouse',
        'kk'   => 'KakaoTalk',
        'zh'   => 'Zalo',
        'zi'   => 'Zalo',
        'zo'   => 'Zoosk',
        'be'   => 'BeReal',
        'lt'   => 'Locket',
        'hi'   => 'Hinge',
        'bu'   => 'Bumble',
        'td'   => 'Tinder',
        'bp'   => 'Badoo',
        'ta'   => 'Tagged',
        'gn'   => 'Grindr',
        'po'   => 'Poshmark',
        'pf'   => 'PicsArt',
        'sc'   => 'Snapchat',
        'tt'   => 'TruthSocial',

        // ── Google / Apple / Microsoft ────────────────────────────────
        'go'   => 'Google',
        'gm'   => 'Gmail',
        'ga'   => 'Google Ads',
        'gv'   => 'Google Voice',
        'gc'   => 'Google Cloud',
        'gs'   => 'Google Pay',
        'ap'   => 'Apple',
        'mm'   => 'Microsoft',
        'az'   => 'Microsoft Azure',
        'of'   => 'Office 365',
        'ou'   => 'Outlook',
        'gl'   => 'Grammarly',

        // ── Crypto & Finance ──────────────────────────────────────────
        'bn'   => 'Binance',
        'cb'   => 'Coinbase',
        'kc'   => 'KuCoin',
        'kr'   => 'Kraken',
        'hu'   => 'Huobi',
        'gt'   => 'Gate.io',
        'cx'   => 'OKX',
        'cy'   => 'Bybit',
        'tr'   => 'Trust Wallet',
        'me2'  => 'MetaMask',
        'ce'   => 'CoinEx',
        'ci'   => 'Circle',
        'pe'   => 'Paxful',
        'pa'   => 'PayPal',
        'rb'   => 'Robinhood',
        'rv'   => 'Revolut',
        'ws'   => 'Wise',
        'mo'   => 'Monese',
        'mn'   => 'Monzo',
        'ef'   => 'eToro',
        'iq'   => 'IQ Option',
        'mt'   => 'MetaTrader',
        'xm'   => 'XM Trading',
        'su'   => 'Stripe',
        'ca'   => 'Cash App',
        'zl'   => 'Zelle',
        'mp'   => 'Mercado Pago',
        'kb'   => 'Kaspi Bank',
        'mc'   => 'Monobank',
        'nk'   => 'Neobank',
        'pp'   => 'Paytm',
        'ph'   => 'PhonePe',
        'gp'   => 'GPay',
        'sp'   => 'SamsungPay',
        'yw'   => 'Yandex Wallet',
        'qw'   => 'QIWI',
        'wm'   => 'WebMoney',

        // ── E-commerce & Delivery ─────────────────────────────────────
        'am'   => 'Amazon',
        'eb'   => 'eBay',
        'al'   => 'AliExpress',
        'la'   => 'Lazada',
        'sh'   => 'Shopify',
        'et'   => 'Etsy',
        'ai'   => 'Airbnb',
        'bk'   => 'Booking.com',
        'xp'   => 'Expedia',
        'jd'   => 'JD.com',
        'sw'   => 'Swiggy',
        'zt'   => 'Zomato',
        'gr'   => 'Grab',
        'bl'   => 'Bolt',
        'ds'   => 'DoorDash',
        'ue'   => 'Uber Eats',
        'fd'   => 'Foodpanda',
        'ra'   => 'Rappi',
        'jb'   => 'Jumia',
        'mc2'  => 'Mercado Libre',
        'sp2'  => 'Shopee',
        'fl'   => 'Flipkart',
        'wl'   => 'Wolt',
        'dd'   => 'Deliveroo',
        'gf'   => 'GrabFood',
        'kf'   => 'KFC',
        'mcd'  => 'McDonald\'s',
        'di'   => 'DiDi',
        'ca2'  => 'Careem',

        // ── Entertainment & Gaming ────────────────────────────────────
        'nf'   => 'Netflix',
        'sa'   => 'Spotify',
        'st'   => 'Steam',
        'ps'   => 'PlayStation',
        'ro'   => 'Roblox',
        'yu'   => 'YouTube',
        'so'   => 'SoundCloud',
        'dz'   => 'Deezer',
        'ti'   => 'Tidal',
        'hp'   => 'Hulu',
        'mx'   => 'Disney+',
        'pr'   => 'Prime Video',
        'at'   => 'Apple TV',
        'hs'   => 'Hotstar',
        'bg'   => 'BIGO Live',
        'lk'   => 'Likee',
        'kw'   => 'Kwai',
        'lo'   => 'LOVO',
        'xb'   => 'Xbox',
        'ep'   => 'Epic Games',
        'va'   => 'Valorant',

        // ── Work & Productivity ───────────────────────────────────────
        'ln'   => 'LinkedIn',
        'ub'   => 'Uber',
        'fi'   => 'Fiverr',
        'up'   => 'Upwork',
        'no'   => 'Notion',
        'sl'   => 'Slack',
        'gi'   => 'GitHub',
        'sf'   => 'Salesforce',
        'pm'   => 'ProtonMail',
        'ne'   => 'Indeed',
        'lm'   => 'Lemfi',
        'zo2'  => 'Zoom',
        'zm'   => 'Zoom',
        'lo2'  => 'Loom',
        'cv'   => 'Canva',
        'dp'   => 'Dropbox',
        'db'   => 'Dribbble',
        'mi'   => 'Miro',

        // ── Yandex ecosystem ─────────────────────────────────────────
        'ya'   => 'Yandex',
        'yt'   => 'Yandex Taxi',
        'ym'   => 'Yandex Music',
        'yp'   => 'Yandex Plus',
        'yd'   => 'Yandex Disk',
        'yg'   => 'Yandex Go',
        'ma'   => 'Mail.ru',
        'av'   => 'Avito',

        // ── Other / Miscellaneous ─────────────────────────────────────
        'tc'   => 'TrueCaller',
        'ct'   => 'ChatGPT',
        'nt'   => 'NordVPN',
        'gj'   => 'GoDaddy',
        'gu'   => 'Gumtree',
        'cl2'  => 'Craigslist',
        'ns'   => 'Nextdoor',
        'ot'   => 'Other',
        'ny'   => 'Any Service',
        'lp'   => 'LetyShops',
        'rc'   => 'Revolut',
        'rl'   => 'Roulettebet',
        'va2'  => 'Vavada',
        '1x'   => '1xBet',
        '1xb'  => '1xBet',
        'bw'   => 'Bet365',
        'mz'   => 'Mostbet',
        'ml'   => 'Melbet',
    ];

    public function __construct()
    {
        $this->apiKey   = trim(Setting::get('grizzlysms_api_key', ''));
        $this->usdToNgn = (float) Setting::get('usd_to_ngn_rate', '1600');
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    private function usdToNgn(float $usd): float
    {
        return round($usd * $this->usdToNgn, 2);
    }

    /** All API calls are plain GET; response is a plain-text string. */
    private function request(array $params): string
    {
        $params['api_key'] = $this->apiKey;
        $response = Http::timeout(30)->get($this->baseUrl, $params);
        Log::info('GrizzlySMS [' . ($params['action'] ?? '?') . '] HTTP ' . $response->status() . ' | ' . substr($response->body(), 0, 200));
        return trim($response->body());
    }

    private function resolveServiceName(string $code): string
    {
        return self::SERVICE_NAMES[$code]
            ?? self::SERVICE_NAMES[strtolower($code)]
            ?? ucwords(str_replace(['_', '-'], ' ', (string) $code));
    }

    // ── Balance ────────────────────────────────────────────────────────────────

    public function getBalance(): array
    {
        try {
            $resp = $this->request(['action' => 'getBalance']);
            if (str_starts_with($resp, 'ACCESS_BALANCE:')) {
                $bal = (float) explode(':', $resp)[1];
                return ['success' => true, 'data' => [
                    'balance_usd' => $bal,
                    'balance_ngn' => $this->usdToNgn($bal),
                    'balance'     => $bal,
                ]];
            }
            return ['success' => false, 'message' => $resp];
        } catch (\Exception $e) {
            Log::error('GrizzlySMS getBalance: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Service temporarily unavailable. Please try again.'];
        }
    }

    // ── Countries ─────────────────────────────────────────────────────────────

    /** Returns hardcoded country list with numeric codes for GrizzlySMS. */
    public function getCountries(): array
    {
        $sorted = self::COUNTRIES;
        usort($sorted, fn($a, $b) => strcmp($a['name'], $b['name']));
        return ['success' => true, 'data' => $sorted];
    }

    // ── Services ──────────────────────────────────────────────────────────────

    /**
     * Fetches services for a specific country.
     * [{serviceId, name, count, cost_usd, cost_ngn}]
     */
    public function getServices(string $countryCode): array
    {
        try {
            $resp = $this->request(['action' => 'getPrices', 'country' => $countryCode]);
            $data = json_decode($resp, true);

            if (!is_array($data) || empty($data)) {
                return ['success' => false, 'message' => 'No services available. Please try again.'];
            }

            $countryInt      = (int) $countryCode;
            $countryServices = $data[$countryInt] ?? $data[$countryCode] ?? null;

            if (!$countryServices && count($data) === 1) {
                $countryServices = reset($data);
            }

            if (!$countryServices || !is_array($countryServices)) {
                return ['success' => false, 'message' => 'No services available for the selected country.'];
            }

            $services = [];
            foreach ($countryServices as $serviceCode => $priceInfo) {
                if (!is_array($priceInfo)) continue;
                $count    = (int) ($priceInfo['count'] ?? 0);
                $priceUsd = (float) ($priceInfo['cost'] ?? 0);
                if ($count <= 0) continue;

                $services[] = [
                    'serviceId' => (string) $serviceCode,
                    'name'      => $this->resolveServiceName((string) $serviceCode),
                    'count'     => $count,
                    'cost_usd'  => $priceUsd,
                    'cost_ngn'  => $this->usdToNgn($priceUsd),
                ];
            }

            if (empty($services)) {
                return ['success' => false, 'message' => 'No services available for the selected country.'];
            }

            usort($services, fn($a, $b) => strcmp($a['name'], $b['name']));
            return ['success' => true, 'data' => $services];
        } catch (\Exception $e) {
            Log::error('GrizzlySMS getServices: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Service temporarily unavailable. Please try again.'];
        }
    }

    /**
     * Fetches ALL services across ALL countries (no country filter).
     * For each service code, picks the cheapest country with stock > 0.
     * Returns [{serviceId, name, count, cost_usd, cost_ngn, best_country_code}]
     */
    public function getAllServices(): array
    {
        try {
            $resp = $this->request(['action' => 'getPrices']);
            $data = json_decode($resp, true);

            if (!is_array($data) || empty($data)) {
                return ['success' => false, 'message' => 'No services available. Please try again.'];
            }

            // Build country code lookup for name resolution
            $countryMap = [];
            foreach (self::COUNTRIES as $c) {
                $countryMap[(string) $c['code']] = $c['name'];
            }

            // Aggregate: for each service code, find cheapest option with stock
            // $best[serviceCode] = ['cost_usd'=>X, 'count'=>N, 'country_code'=>'C']
            $best = [];

            foreach ($data as $countryCode => $services) {
                if (!is_array($services)) continue;
                $countryStr = (string) $countryCode;

                foreach ($services as $serviceCode => $priceInfo) {
                    if (!is_array($priceInfo)) continue;
                    $count    = (int) ($priceInfo['count'] ?? 0);
                    $priceUsd = (float) ($priceInfo['cost'] ?? 0);

                    if ($count <= 0) continue;

                    $code = (string) $serviceCode;
                    if (
                        !isset($best[$code]) ||
                        $priceUsd < $best[$code]['cost_usd'] ||
                        ($priceUsd === $best[$code]['cost_usd'] && $count > $best[$code]['count'])
                    ) {
                        $best[$code] = [
                            'cost_usd'     => $priceUsd,
                            'count'        => $count,
                            'country_code' => $countryStr,
                        ];
                    }
                }
            }

            if (empty($best)) {
                return ['success' => false, 'message' => 'No services available at this time.'];
            }

            $services = [];
            foreach ($best as $serviceCode => $info) {
                $services[] = [
                    'serviceId'         => $serviceCode,
                    'name'              => $this->resolveServiceName($serviceCode),
                    'count'             => $info['count'],
                    'cost_usd'          => $info['cost_usd'],
                    'cost_ngn'          => $this->usdToNgn($info['cost_usd']),
                    'best_country_code' => $info['country_code'],
                    'best_country_name' => $countryMap[$info['country_code']] ?? 'International',
                ];
            }

            usort($services, fn($a, $b) => strcmp($a['name'], $b['name']));
            return ['success' => true, 'data' => $services];
        } catch (\Exception $e) {
            Log::error('GrizzlySMS getAllServices: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Service temporarily unavailable. Please try again.'];
        }
    }

    // ── Order ─────────────────────────────────────────────────────────────────

    /**
     * Orders a virtual number.
     * Returns ['success'=>true, 'data'=>['order_id','number']]
     */
    public function orderNumber(string $countryCode, string $serviceCode): array
    {
        try {
            $params = [
                'action'  => 'getNumber',
                'service' => $serviceCode,
            ];
            if ($countryCode !== '') {
                $params['country'] = $countryCode;
            }

            $resp = $this->request($params);
            Log::info('GrizzlySMS orderNumber [' . $countryCode . '/' . $serviceCode . '] response: ' . $resp);

            if (str_starts_with($resp, 'ACCESS_NUMBER:')) {
                $parts = explode(':', $resp, 3);
                return ['success' => true, 'data' => [
                    'order_id' => $parts[1] ?? '',
                    'number'   => $parts[2] ?? '',
                ]];
            }

            $msg = match($resp) {
                'NO_NUMBERS'  => 'Out of stock. Please try again later.',
                'NO_BALANCE'  => 'Out of stock. Please try again later.',
                'BAD_KEY'     => 'Service configuration error. Please contact support.',
                'BAD_SERVICE' => 'Out of stock. Please try again later.',
                'BAD_COUNTRY' => 'This country is not supported.',
                default       => 'Could not get a number. Please try again.',
            };
            return ['success' => false, 'message' => $msg, 'raw' => $resp];
        } catch (\Exception $e) {
            Log::error('GrizzlySMS orderNumber: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Out of stock. Please try again later.'];
        }
    }

    // ── Check SMS ─────────────────────────────────────────────────────────────

    /**
     * Checks activation status / SMS code.
     * status: pending|received|cancelled
     */
    public function checkSms(string $orderId): array
    {
        try {
            $resp = $this->request(['action' => 'getStatus', 'id' => (int) $orderId]);

            if (str_starts_with($resp, 'STATUS_OK:')) {
                $code = substr($resp, strlen('STATUS_OK:'));
                return ['success' => true, 'data' => ['status' => 'received', 'sms' => $code]];
            }
            if (str_starts_with($resp, 'STATUS_WAIT_RETRY:')) {
                $code = substr($resp, strlen('STATUS_WAIT_RETRY:'));
                return ['success' => true, 'data' => ['status' => 'pending', 'sms' => $code ?: null]];
            }
            if ($resp === 'STATUS_CANCEL') {
                return ['success' => true, 'data' => ['status' => 'cancelled', 'sms' => null]];
            }
            if (in_array($resp, ['BAD_KEY', 'BAD_ACTION', 'NO_ACTIVATION', 'WRONG_ACTIVATION_ID'])) {
                Log::warning('GrizzlySMS checkSms error response: ' . $resp . ' for order ' . $orderId);
                return ['success' => false, 'message' => 'Activation error: ' . $resp];
            }
            return ['success' => true, 'data' => ['status' => 'pending', 'sms' => null]];
        } catch (\Exception $e) {
            Log::error('GrizzlySMS checkSms: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Could not reach GrizzlySMS API.'];
        }
    }

    // ── Cancel ────────────────────────────────────────────────────────────────

    public function cancelOrder(string $orderId): array
    {
        try {
            $resp = $this->request(['action' => 'setStatus', 'id' => $orderId, 'status' => 8]);
            if ($resp === 'ACCESS_CANCEL') {
                return ['success' => true, 'message' => 'Order cancelled successfully.'];
            }
            $msg = match($resp) {
                'BAD_KEY'       => 'Invalid GrizzlySMS API key.',
                'BAD_ACTION'    => 'Invalid action.',
                'NO_ACTIVATION' => 'Activation not found.',
                'BAD_STATUS'    => 'Cannot cancel at this stage.',
                default         => 'Cancel response: ' . $resp,
            };
            if (in_array($resp, ['ACCESS_CANCEL', 'STATUS_CANCEL', 'STATUS_OK'])) {
                return ['success' => true, 'message' => $resp];
            }
            return ['success' => false, 'message' => $msg];
        } catch (\Exception $e) {
            Log::error('GrizzlySMS cancelOrder: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Could not reach GrizzlySMS API.'];
        }
    }
}
