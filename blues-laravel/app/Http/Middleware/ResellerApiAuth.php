<?php
namespace App\Http\Middleware;

use App\Models\ApiKey;
use App\Models\ApiRequestLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class ResellerApiAuth
{
    public function handle(Request $request, Closure $next)
    {
        $startMs = (int) round(microtime(true) * 1000);

        // ── 1. Extract key ───────────────────────────────────────────────────
        $rawKey = $request->header('X-API-Key') ?? $request->query('api_key');

        if (!$rawKey) {
            return $this->json(['error' => 'API key missing. Send X-API-Key header.'], 401);
        }

        // ── 2. Sanitize ──────────────────────────────────────────────────────
        if (!preg_match('/^[a-zA-Z0-9_\-]{10,80}$/', $rawKey)) {
            return $this->json(['error' => 'Invalid API key format.'], 401);
        }

        // ── 3. Rate limit — 60 req/min per key ──────────────────────────────
        $rateLimitKey = 'api_key:' . hash('sha256', $rawKey);
        if (RateLimiter::tooManyAttempts($rateLimitKey, 60)) {
            $retryAfter = RateLimiter::availableIn($rateLimitKey);
            return $this->json(
                ['error' => 'Too many requests. Try again in ' . $retryAfter . ' seconds.'],
                429,
                ['Retry-After' => $retryAfter, 'X-RateLimit-Limit' => 60, 'X-RateLimit-Remaining' => 0]
            );
        }
        RateLimiter::hit($rateLimitKey, 60);

        // ── 4. Lookup key ────────────────────────────────────────────────────
        $record = ApiKey::where('key', $rawKey)
            ->where('is_active', true)
            ->with('user')
            ->first();

        if (!$record) {
            return $this->json(['error' => 'Invalid or revoked API key.'], 401);
        }

        // ── 5. Check user account is active ──────────────────────────────────
        $user = $record->user;
        if (!$user) {
            return $this->json(['error' => 'Account not found.'], 403);
        }

        // ── 6. Bind user to request ──────────────────────────────────────────
        $request->merge(['_api_user' => $user, '_api_key_id' => $record->id]);
        $record->update(['last_used_at' => now()]);

        // ── 7. Pass through ──────────────────────────────────────────────────
        $response = $next($request);

        // ── 8. Security headers ──────────────────────────────────────────────
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'no-referrer');
        $response->headers->set('X-RateLimit-Limit', '60');
        $response->headers->set('X-RateLimit-Remaining', (string) max(0, 60 - RateLimiter::attempts($rateLimitKey)));

        // ── 9. Log request ───────────────────────────────────────────────────
        $elapsedMs = (int) round(microtime(true) * 1000) - $startMs;
        try {
            ApiRequestLog::create([
                'api_key_id'  => $record->id,
                'user_id'     => $user->id,
                'method'      => $request->method(),
                'path'        => '/' . ltrim($request->path(), '/'),
                'status_code' => $response->getStatusCode(),
                'ip'          => $request->ip(),
                'user_agent'  => substr($request->userAgent() ?? '', 0, 512),
                'response_ms' => min($elapsedMs, 65535),
                'created_at'  => now(),
            ]);
        } catch (\Throwable) {}

        return $response;
    }

    private function json(array $data, int $status, array $headers = [])
    {
        $resp = response()->json($data, $status);
        $resp->headers->set('X-Content-Type-Options', 'nosniff');
        foreach ($headers as $k => $v) {
            $resp->headers->set($k, $v);
        }
        return $resp;
    }
}
