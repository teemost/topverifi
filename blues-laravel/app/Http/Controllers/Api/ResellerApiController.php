<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BoostingOrder;
use App\Models\Setting;
use App\Models\VirtualNumberOrder;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\FiveSimService;
use App\Services\GrizzlySmsService;
use App\Services\HeroSmsService;
use App\Services\JapService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResellerApiController extends Controller
{
    private function apiUser(Request $request)
    {
        return $request->get('_api_user');
    }

    // ── Balance ───────────────────────────────────────────────────────────────

    public function balance(Request $request)
    {
        $user   = $this->apiUser($request);
        $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);

        return response()->json([
            'balance'  => (float) $wallet->balance,
            'currency' => 'NGN',
        ]);
    }

    // ── SMM Services ──────────────────────────────────────────────────────────

    public function smmServices(Request $request)
    {
        if (Setting::get('boosting_enabled', '1') !== '1') {
            return response()->json(['error' => 'SMM Boosting is currently disabled.'], 503);
        }
        if (empty(Setting::get('jap_api_key', ''))) {
            return response()->json(['error' => 'SMM Boosting is not configured.'], 503);
        }

        try {
            $jap  = new JapService();
            $raw  = $jap->getServices();
            $markup = (float) Setting::get('boosting_markup_percent', '0');

            $services = array_map(function ($s) use ($markup) {
                $base  = (float) ($s['rate'] ?? 0);
                $price = $markup > 0 ? round($base * (1 + $markup / 100), 4) : $base;
                return [
                    'service_id'  => $s['service'] ?? $s['id'] ?? null,
                    'name'        => $s['name'] ?? '',
                    'category'    => $s['category'] ?? '',
                    'min'         => (int) ($s['min'] ?? 0),
                    'max'         => (int) ($s['max'] ?? 0),
                    'rate_per_1k' => $price,
                ];
            }, $raw);

            return response()->json(['services' => $services]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch services. Try again later.'], 502);
        }
    }

    // ── Place SMM Order ───────────────────────────────────────────────────────

    public function smmOrder(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|integer',
            'link'       => 'required|url|max:500',
            'quantity'   => 'required|integer|min:1',
        ]);

        if (Setting::get('boosting_enabled', '1') !== '1') {
            return response()->json(['error' => 'SMM Boosting is currently disabled.'], 503);
        }

        $user   = $this->apiUser($request);
        $wallet = Wallet::where('user_id', $user->id)->first();

        // Determine price from JAP + markup
        try {
            $jap      = new JapService();
            $services = $jap->getServices();
            $svc      = collect($services)->firstWhere('service', $validated['service_id'])
                     ?? collect($services)->firstWhere('id', $validated['service_id']);

            if (!$svc) {
                return response()->json(['error' => 'Service not found.'], 404);
            }

            $markup = (float) Setting::get('boosting_markup_percent', '0');
            $base   = (float) ($svc['rate'] ?? 0);
            $rate   = $markup > 0 ? round($base * (1 + $markup / 100), 4) : $base;
            $charge = round(($validated['quantity'] / 1000) * $rate, 2);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Could not calculate price. Try again.'], 502);
        }

        if (!$wallet || $wallet->balance < $charge) {
            return response()->json(['error' => 'Insufficient wallet balance.'], 402);
        }

        DB::beginTransaction();
        try {
            $japOrderId = null;
            if (!empty(Setting::get('jap_api_key', ''))) {
                $japOrderId = $jap->placeOrder(
                    (int) $validated['service_id'],
                    $validated['link'],
                    (int) $validated['quantity']
                );
            }

            $wallet->balance -= $charge;
            $wallet->save();

            WalletTransaction::create([
                'user_id'     => $user->id,
                'type'        => 'debit',
                'amount'      => $charge,
                'description' => 'API SMM Order: ' . ($svc['name'] ?? $validated['service_id']),
                'reference'   => 'API-SMM-' . ($japOrderId ?? uniqid()),
            ]);

            $order = BoostingOrder::create([
                'user_id'      => $user->id,
                'jap_order_id' => $japOrderId,
                'service_id'   => $validated['service_id'],
                'service_name' => $svc['name'] ?? (string) $validated['service_id'],
                'category'     => $svc['category'] ?? '',
                'link'         => $validated['link'],
                'quantity'     => $validated['quantity'],
                'charge'       => $charge,
                'status'       => 'pending',
            ]);

            DB::commit();

            return response()->json([
                'order_id'  => $order->id,
                'status'    => $order->status,
                'charge'    => $charge,
                'currency'  => 'NGN',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Order failed: ' . $e->getMessage()], 500);
        }
    }

    // ── SMM Order Status ──────────────────────────────────────────────────────

    public function smmOrderStatus(Request $request, int $id)
    {
        $user  = $this->apiUser($request);
        $order = BoostingOrder::where('user_id', $user->id)->find($id);

        if (!$order) {
            return response()->json(['error' => 'Order not found.'], 404);
        }

        if ($order->jap_order_id) {
            try {
                $jap    = new JapService();
                $status = $jap->getOrderStatus($order->jap_order_id);
                $order->update([
                    'status'      => strtolower($status['status'] ?? $order->status),
                    'start_count' => $status['start_count'] ?? $order->start_count,
                    'remains'     => $status['remains'] ?? $order->remains,
                ]);
                $order->refresh();
            } catch (\Exception) {}
        }

        return response()->json([
            'order_id'    => $order->id,
            'service'     => $order->service_name,
            'link'        => $order->link,
            'quantity'    => $order->quantity,
            'start_count' => $order->start_count,
            'remains'     => $order->remains,
            'status'      => $order->status,
            'charge'      => (float) $order->charge,
            'currency'    => 'NGN',
            'created_at'  => $order->created_at->toIso8601String(),
        ]);
    }

    // ── Number Countries ──────────────────────────────────────────────────────

    public function numberCountries(Request $request)
    {
        $provider = $request->query('provider', 'grizzlysms');

        switch ($provider) {
            case 'fivesim':
                $svc    = new FiveSimService();
                $result = $svc->isConfigured() ? $svc->getCountries() : ['success' => false, 'message' => 'Not configured'];
                break;
            case 'herosms':
                $svc    = new HeroSmsService();
                $result = $svc->isConfigured() ? $svc->getCountries() : ['success' => false, 'message' => 'Not configured'];
                break;
            default:
                $svc    = new GrizzlySmsService();
                $result = $svc->isConfigured() ? $svc->getCountries() : ['success' => false, 'message' => 'Not configured'];
        }

        if (!$result['success']) {
            return response()->json(['error' => $result['message'] ?? 'Failed to load countries.'], 502);
        }

        return response()->json(['countries' => $result['data']]);
    }

    // ── Number Services ───────────────────────────────────────────────────────

    public function numberServices(Request $request)
    {
        $provider = $request->query('provider', 'grizzlysms');
        $country  = $request->query('country');

        switch ($provider) {
            case 'fivesim':
                $svc    = new FiveSimService();
                $result = $svc->isConfigured()
                    ? ($country ? $svc->getServices($country) : ['success' => false, 'message' => 'country required'])
                    : ['success' => false, 'message' => 'Not configured'];
                break;
            case 'herosms':
                $svc    = new HeroSmsService();
                $result = $svc->isConfigured() ? $svc->getServices($country) : ['success' => false, 'message' => 'Not configured'];
                break;
            default:
                $svc    = new GrizzlySmsService();
                $result = $svc->isConfigured()
                    ? ($country ? $svc->getServices($country) : $svc->getAllServices())
                    : ['success' => false, 'message' => 'Not configured'];
        }

        if (!$result['success']) {
            return response()->json(['error' => $result['message'] ?? 'Failed to load services.'], 502);
        }

        return response()->json(['services' => $result['data']]);
    }

    // ── Order Virtual Number ──────────────────────────────────────────────────

    public function numberOrder(Request $request)
    {
        $request->validate([
            'provider'     => 'required|string|in:grizzlysms,fivesim,herosms',
            'service_id'   => 'required|string',
            'country'      => 'nullable|string',
            'price'        => 'nullable|numeric|min:0',
            'service_name' => 'nullable|string',
        ]);

        if (Setting::get('virtual_number_enabled', '1') !== '1') {
            return response()->json(['error' => 'Virtual numbers are currently unavailable.'], 503);
        }

        $user    = $this->apiUser($request);
        $wallet  = Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);
        $apiCost = (float) ($request->price ?? 0);
        $commType  = Setting::get('vn_commission_type', 'flat');
        $commValue = (float) Setting::get('vn_commission_value', '0');
        $commission = $commType === 'percent' ? round($apiCost * $commValue / 100, 2) : $commValue;
        $cost = round($apiCost + $commission, 2);

        if ($cost > 0 && $wallet->balance < $cost) {
            return response()->json(['error' => 'Insufficient wallet balance.'], 402);
        }

        $serviceName = $request->service_name ?? $request->service_id;

        switch ($request->provider) {
            case 'fivesim':
                $svc = new FiveSimService();
                break;
            case 'herosms':
                $svc = new HeroSmsService();
                break;
            default:
                $svc = new GrizzlySmsService();
        }

        if (!$svc->isConfigured()) {
            return response()->json(['error' => 'Selected provider is not available.'], 503);
        }

        $result = $svc->orderNumber($request->country ?? '', $request->service_id);
        if (!$result['success']) {
            return response()->json(['error' => $result['message'] ?? 'Failed to place order.'], 502);
        }

        $data  = $result['data'];
        $order = null;

        DB::transaction(function () use ($data, $request, $cost, $wallet, $serviceName, $user, &$order) {
            $order = VirtualNumberOrder::create([
                'user_id'           => $user->id,
                'provider'          => $request->provider,
                'external_order_id' => (string) ($data['order_id'] ?? ''),
                'service'           => $serviceName,
                'country'           => $request->country ?? '',
                'phone_number'      => $data['number'] ?? null,
                'cost'              => $cost,
                'status'            => 'active',
                'raw_response'      => json_encode($data),
            ]);

            if ($cost > 0) {
                $wallet->decrement('balance', $cost);
                WalletTransaction::create([
                    'user_id'     => $user->id,
                    'type'        => 'withdrawal',
                    'amount'      => $cost,
                    'description' => 'API Virtual number: ' . $serviceName,
                    'reference'   => 'API-VN-' . $order->id . '-' . time(),
                ]);
            }
        });

        return response()->json([
            'order_id'     => $order->id,
            'phone_number' => $order->phone_number,
            'provider'     => $order->provider,
            'service'      => $order->service,
            'country'      => $order->country,
            'cost'         => (float) $order->cost,
            'currency'     => 'NGN',
            'status'       => $order->status,
        ], 201);
    }

    // ── Check SMS ─────────────────────────────────────────────────────────────

    public function numberSms(Request $request, int $id)
    {
        $user  = $this->apiUser($request);
        $order = VirtualNumberOrder::where('user_id', $user->id)->find($id);

        if (!$order) {
            return response()->json(['error' => 'Order not found.'], 404);
        }

        switch ($order->provider) {
            case 'fivesim': $svc = new FiveSimService(); break;
            case 'herosms': $svc = new HeroSmsService(); break;
            default:        $svc = new GrizzlySmsService();
        }

        $result = $svc->checkSms($order->external_order_id);

        if ($result['success']) {
            $data  = $result['data'];
            $sms   = $data['sms'] ?? null;
            $order->update([
                'sms_code' => $sms ?: $order->sms_code,
            ]);
        }

        $order->refresh();

        return response()->json([
            'order_id'     => $order->id,
            'phone_number' => $order->phone_number,
            'sms_code'     => $order->sms_code,
            'status'       => $order->status,
        ]);
    }

    // ── Cancel Number ─────────────────────────────────────────────────────────

    public function numberCancel(Request $request, int $id)
    {
        $user  = $this->apiUser($request);
        $order = VirtualNumberOrder::where('user_id', $user->id)
                    ->where('status', 'active')
                    ->find($id);

        if (!$order) {
            return response()->json(['error' => 'Active order not found.'], 404);
        }

        switch ($order->provider) {
            case 'fivesim': $svc = new FiveSimService(); break;
            case 'herosms': $svc = new HeroSmsService(); break;
            default:        $svc = new GrizzlySmsService();
        }

        if ($order->external_order_id) {
            $svc->cancelOrder($order->external_order_id);
        }

        // Refund
        $wallet = Wallet::where('user_id', $user->id)->first();
        if ($wallet && $order->cost > 0) {
            $wallet->increment('balance', $order->cost);
            WalletTransaction::create([
                'user_id'     => $user->id,
                'type'        => 'refund',
                'amount'      => $order->cost,
                'description' => 'Refund: API Virtual number cancelled',
                'reference'   => 'REFUND-API-VN-' . $order->id,
            ]);
        }

        $order->update(['status' => 'cancelled']);

        return response()->json([
            'order_id' => $order->id,
            'status'   => 'cancelled',
            'refunded' => $order->cost > 0,
        ]);
    }
}
