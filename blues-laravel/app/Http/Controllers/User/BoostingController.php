<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BoostingOrder;
use App\Models\PurchaseErrorLog;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Setting;
use App\Services\JapService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BoostingController extends Controller
{
    public function index(Request $request)
    {
        $enabled = Setting::get('boosting_enabled', '1') === '1';
        if (!$enabled) {
            return view('dashboard.boosting-disabled');
        }

        $services = [];
        $categories = [];
        $error = null;
        $japConfigured = !empty(Setting::get('jap_api_key', ''));

        if ($japConfigured) {
            try {
                $jap = new JapService();
                $raw = $jap->getServices();
                foreach ($raw as $svc) {
                    $cat = $svc['category'] ?? 'Other';
                    $categories[$cat][] = $svc;
                }
                ksort($categories);
            } catch (\Exception $e) {
                Log::error('JAP getServices failed: ' . $e->getMessage());
                $error = 'SMM Boosting is temporarily unavailable. Please try again later.';
            }
        } else {
            $error = 'SMM Boosting is being set up. Check back soon!';
        }

        $wallet = Wallet::firstOrCreate(['user_id' => Auth::id()], ['balance' => 0]);

        return view('dashboard.boosting', compact('categories', 'wallet', 'error', 'japConfigured'));
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'service_id'   => 'required|integer',
            'service_name' => 'required|string|max:255',
            'category'     => 'nullable|string|max:255',
            'link'         => 'required|url|max:500',
            'quantity'     => 'required|integer|min:1',
            'charge'       => 'required|numeric|min:0',
        ]);

        $user = Auth::user();
        $wallet = Wallet::where('user_id', $user->id)->first();

        if (!$wallet || $wallet->balance < $request->charge) {
            return back()->with('error', 'Insufficient wallet balance. Please top up your wallet.');
        }

        DB::beginTransaction();
        try {
            $japOrderId = null;
            $japConfigured = !empty(Setting::get('jap_api_key', ''));

            if ($japConfigured) {
                $jap = new JapService();
                $japOrderId = $jap->placeOrder(
                    (int) $request->service_id,
                    $request->link,
                    (int) $request->quantity
                );
            }

            $wallet->balance -= $request->charge;
            $wallet->save();

            WalletTransaction::create([
                'user_id'     => $user->id,
                'type'        => 'debit',
                'amount'      => $request->charge,
                'description' => 'SMM Order: ' . $request->service_name,
                'reference'   => 'SMM-' . ($japOrderId ?? uniqid()),
            ]);

            $order = BoostingOrder::create([
                'user_id'      => $user->id,
                'jap_order_id' => $japOrderId,
                'service_id'   => $request->service_id,
                'service_name' => $request->service_name,
                'category'     => $request->category,
                'link'         => $request->link,
                'quantity'     => $request->quantity,
                'charge'       => $request->charge,
                'status'       => $japOrderId ? 'pending' : 'pending',
            ]);

            DB::commit();

            return redirect()->route('dashboard.boosting-orders')
                ->with('success', 'Order placed successfully! Your order is now being processed.');
        } catch (\Exception $e) {
            DB::rollBack();
            PurchaseErrorLog::record('jap', 'boosting', $e->getMessage(), [
                'service_id'   => $request->service_id,
                'service_name' => $request->service_name,
                'link'         => $request->link,
                'quantity'     => $request->quantity,
                'charge'       => $request->charge,
            ], Auth::id());
            Log::error('Boosting order failed: ' . $e->getMessage());
            return back()->with('error', 'Order failed: ' . $e->getMessage());
        }
    }

    public function orders(Request $request)
    {
        $orders = BoostingOrder::where('user_id', Auth::id())
            ->latest()
            ->paginate(20);

        return view('dashboard.boosting-orders', compact('orders'));
    }

    public function checkStatus(int $id)
    {
        $order = BoostingOrder::where('user_id', Auth::id())->findOrFail($id);

        if ($order->jap_order_id) {
            try {
                $jap = new JapService();
                $status = $jap->getOrderStatus($order->jap_order_id);
                $order->update([
                    'status'      => strtolower($status['status'] ?? $order->status),
                    'start_count' => $status['start_count'] ?? $order->start_count,
                    'remains'     => $status['remains'] ?? $order->remains,
                ]);
            } catch (\Exception $e) {
                // silently fail
            }
        }

        return back()->with('success', 'Order status refreshed.');
    }

    public function pollStatus(Request $request)
    {
        $activeStatuses = ['pending', 'in_progress', 'processing'];

        $orders = BoostingOrder::where('user_id', Auth::id())
            ->latest()
            ->take(50)
            ->get(['id', 'jap_order_id', 'status', 'remains', 'start_count', 'quantity']);

        // Auto-sync active orders with JAP (up to 5 per poll to avoid rate limits)
        $toSync = $orders->filter(fn($o) => in_array($o->status, $activeStatuses) && $o->jap_order_id)->take(5);

        if ($toSync->isNotEmpty()) {
            try {
                $jap = new JapService();
                foreach ($toSync as $order) {
                    try {
                        $status = $jap->getOrderStatus($order->jap_order_id);
                        $order->update([
                            'status'      => strtolower($status['status'] ?? $order->status),
                            'start_count' => $status['start_count'] ?? $order->start_count,
                            'remains'     => $status['remains'] ?? $order->remains,
                        ]);
                        $order->refresh();
                    } catch (\Exception $e) {
                        // skip individual failures
                    }
                }
            } catch (\Exception $e) {
                // JAP unavailable — return current DB state
            }
        }

        $payload = $orders->map(function ($order) {
            return [
                'id'          => $order->id,
                'status'      => $order->status,
                'status_label'=> ucfirst(str_replace('_', ' ', $order->status)),
                'status_badge'=> $order->status_badge,
                'remains'     => $order->remains,
                'start_count' => $order->start_count,
                'progress'    => $order->start_count && $order->quantity
                    ? round((($order->quantity - max(0, $order->remains ?? $order->quantity)) / $order->quantity) * 100)
                    : null,
            ];
        });

        $hasActive = $orders->contains(fn($o) => in_array($o->status, $activeStatuses));

        return response()->json([
            'orders'    => $payload,
            'has_active'=> $hasActive,
            'polled_at' => now()->toISOString(),
        ]);
    }
}
