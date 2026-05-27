<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BoostingOrder;
use App\Services\JapService;
use App\Models\Setting;
use Illuminate\Http\Request;

class BoostingOrdersController extends Controller
{
    public function index(Request $request)
    {
        $query = BoostingOrder::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', '%'.$request->search.'%')
                ->orWhere('email', 'like', '%'.$request->search.'%'));
        }

        $orders = $query->paginate(30);

        $stats = [
            'total'       => BoostingOrder::count(),
            'pending'     => BoostingOrder::where('status', 'pending')->count(),
            'in_progress' => BoostingOrder::where('status', 'in_progress')->count(),
            'completed'   => BoostingOrder::where('status', 'completed')->count(),
        ];

        $japBalance = null;
        if (!empty(Setting::get('jap_api_key', ''))) {
            try {
                $jap = new JapService();
                $japBalance = $jap->getBalance();
            } catch (\Exception $e) {}
        }

        return view('admin.boosting-orders', compact('orders', 'stats', 'japBalance'));
    }

    public function syncStatus(int $id)
    {
        $order = BoostingOrder::findOrFail($id);

        if (!$order->jap_order_id) {
            return back()->with('error', 'No JAP order ID to sync.');
        }

        try {
            $jap = new JapService();
            $status = $jap->getOrderStatus($order->jap_order_id);
            $order->update([
                'status'      => strtolower($status['status'] ?? $order->status),
                'start_count' => $status['start_count'] ?? $order->start_count,
                'remains'     => $status['remains'] ?? $order->remains,
            ]);
            return back()->with('success', 'Order #'.$id.' synced: '.$order->fresh()->status);
        } catch (\Exception $e) {
            return back()->with('error', 'Sync failed: '.$e->getMessage());
        }
    }

    public function syncAll()
    {
        $orders = BoostingOrder::whereNotNull('jap_order_id')
            ->whereNotIn('status', ['completed', 'cancelled', 'canceled'])
            ->get();

        $synced = 0;
        if (!empty(Setting::get('jap_api_key', ''))) {
            try {
                $jap = new JapService();
                foreach ($orders as $order) {
                    try {
                        $status = $jap->getOrderStatus($order->jap_order_id);
                        $order->update([
                            'status'      => strtolower($status['status'] ?? $order->status),
                            'start_count' => $status['start_count'] ?? $order->start_count,
                            'remains'     => $status['remains'] ?? $order->remains,
                        ]);
                        $synced++;
                    } catch (\Exception $e) {}
                }
            } catch (\Exception $e) {
                return back()->with('error', 'Sync failed: '.$e->getMessage());
            }
        }

        return back()->with('success', "Synced {$synced} orders.");
    }
}
