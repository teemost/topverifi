<?php

/**
 * Hero-SMS API configuration.
 *
 * Credentials are read from the .env file (preferred) with a fallback to the
 * database Settings table so existing admin-UI-configured keys keep working.
 * All tunable limits live here so they can be overridden per environment
 * without touching service code.
 */
return [

    /*
    |--------------------------------------------------------------------------
    | API Credentials
    |--------------------------------------------------------------------------
    |
    | Set HEROSMS_API_KEY in your .env file.  If the env var is empty the
    | HeroSmsService will fall back to the database setting 'herosms_api_key'
    | (managed through the admin panel) so existing deployments are unaffected.
    |
    */
    'api_key'  => env('HEROSMS_API_KEY', ''),
    'base_url' => env('HEROSMS_BASE_URL', 'https://hero-sms.com/stubs/handler_api.php'),

    /*
    |--------------------------------------------------------------------------
    | HTTP Client
    |--------------------------------------------------------------------------
    */
    'timeout' => (int) env('HEROSMS_TIMEOUT', 20),  // seconds per request

    /*
    |--------------------------------------------------------------------------
    | Retry / Backoff
    |--------------------------------------------------------------------------
    |
    | max_retries     – how many times to re-request a number when a restricted,
    |                   unavailable, banned, sold, expired, or cancelled number
    |                   is returned.
    |
    | base_delay_ms   – initial pause before the first retry (milliseconds).
    |                   Each subsequent retry doubles the delay (exponential
    |                   backoff): attempt 1 → base, attempt 2 → base×2, …
    |
    | max_delay_ms    – ceiling so retries never wait longer than this.
    |
    | rate_limit_ms   – extra pause inserted when the API returns HTTP 429.
    |
    */
    'max_retries'     => (int) env('HEROSMS_MAX_RETRIES', 5),
    'base_delay_ms'   => (int) env('HEROSMS_BASE_DELAY_MS', 1000),
    'max_delay_ms'    => (int) env('HEROSMS_MAX_DELAY_MS', 16000),
    'rate_limit_ms'   => (int) env('HEROSMS_RATE_LIMIT_MS', 5000),

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | log_channel – which Laravel log channel to write Hero-SMS log entries to.
    |               Defaults to the application default channel.
    |
    */
    'log_channel' => env('HEROSMS_LOG_CHANNEL', env('LOG_CHANNEL', 'stack')),

];
