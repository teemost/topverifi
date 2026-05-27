<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\{BoostingOrder, Notification, Wallet, VirtualNumberOrder, User};
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user          = Auth::user();
        $wallet        = Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);
        $recentOrders  = BoostingOrder::where('user_id', $user->id)->latest()->limit(5)->get();
        $orderCount    = BoostingOrder::where('user_id', $user->id)->count();
        $vnCount       = VirtualNumberOrder::where('user_id', $user->id)->count();
        $referralCount = User::where('referred_by', $user->id)->count();
        $unreadCount   = Notification::where('user_id', $user->id)->where('is_read', false)->count();

        return view('dashboard.index', compact('wallet', 'recentOrders', 'unreadCount', 'orderCount', 'vnCount', 'referralCount'));
    }
}
