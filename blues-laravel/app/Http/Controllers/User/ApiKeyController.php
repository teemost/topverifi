<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiKeyController extends Controller
{
    public function index()
    {
        $keys = ApiKey::where('user_id', Auth::id())->latest()->get();
        return view('dashboard.api-keys', compact('keys'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $limit = 5;
        if (ApiKey::where('user_id', Auth::id())->count() >= $limit) {
            return back()->with('error', "You can only have up to {$limit} API keys.");
        }

        ApiKey::create([
            'user_id'   => Auth::id(),
            'name'      => $request->name,
            'key'       => ApiKey::generate(),
            'is_active' => true,
        ]);

        return back()->with('success', 'API key created successfully.');
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
        return back()->with('success', 'API key revoked.');
    }
}
