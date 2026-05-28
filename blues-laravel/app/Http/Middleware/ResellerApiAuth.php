<?php
namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;

class ResellerApiAuth
{
    public function handle(Request $request, Closure $next)
    {
        $key = $request->header('X-API-Key') ?? $request->query('api_key');

        if (!$key) {
            return response()->json(['error' => 'API key missing. Pass X-API-Key header.'], 401);
        }

        $record = ApiKey::where('key', $key)->where('is_active', true)->with('user')->first();

        if (!$record) {
            return response()->json(['error' => 'Invalid or revoked API key.'], 401);
        }

        $record->update(['last_used_at' => now()]);

        $request->merge(['_api_user' => $record->user]);

        return $next($request);
    }
}
