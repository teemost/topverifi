<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Models\ApiRequestLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiKeyController extends Controller
{
    public function index()
    {
        $keys = ApiKey::where('user_id', Auth::id())->latest()->get();

        $logs = ApiRequestLog::where('user_id', Auth::id())
            ->with('apiKey:id,name')
            ->orderByDesc('created_at')
            ->limit(100)
            ->get();

        $totalCalls   = $logs->count();
        $successCalls = $logs->where('status_code', '<', 300)->count();
        $errorCalls   = $logs->where('status_code', '>=', 400)->count();
        $avgMs        = $logs->avg('response_ms') ? (int) round($logs->avg('response_ms')) : 0;

        return view('dashboard.api-keys', compact('keys', 'logs', 'totalCalls', 'successCalls', 'errorCalls', 'avgMs'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100']);

        if (ApiKey::where('user_id', Auth::id())->count() >= 5) {
            return back()->with('error', 'You can only have up to 5 API keys.');
        }

        ApiKey::create([
            'user_id'   => Auth::id(),
            'name'      => $request->name,
            'key'       => ApiKey::generate(),
            'is_active' => true,
        ]);

        return back()->with('success', 'API key created. Copy it now — it will be masked after this page loads.');
    }

    public function toggle(int $id)
    {
        $key = ApiKey::where('user_id', Auth::id())->findOrFail($id);
        $key->update(['is_active' => !$key->is_active]);
        return back()->with('success', 'API key ' . ($key->is_active ? 'enabled' : 'disabled') . '.');
    }

    public function destroy(int $id)
    {
        ApiKey::where('user_id', Auth::id())->findOrFail($id)->delete();
        return back()->with('success', 'API key permanently revoked.');
    }
}
