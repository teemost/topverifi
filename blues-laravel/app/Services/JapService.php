<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Setting;

class JapService
{
    protected string $apiUrl = 'https://justanotherpanel.com/api/v2';
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = Setting::get('jap_api_key', '');
    }

    protected function post(array $params): mixed
    {
        if (empty($this->apiKey)) {
            throw new \Exception('JustAnotherPanel API key is not configured.');
        }

        $response = Http::asForm()->timeout(30)->post($this->apiUrl, array_merge([
            'key' => $this->apiKey,
        ], $params));

        $data = null;
        try {
            $data = $response->json();
        } catch (\Throwable) {
        }

        if (!$response->successful()) {
            $japError = $data['error'] ?? null;
            if ($japError) {
                throw new \Exception('JAP error: ' . $japError);
            }
            throw new \Exception('JAP API request failed: HTTP ' . $response->status());
        }

        if (isset($data['error'])) {
            throw new \Exception('JAP error: ' . $data['error']);
        }

        return $data;
    }

    public function getBalance(): float
    {
        $data = $this->post(['action' => 'balance']);
        return (float) ($data['balance'] ?? 0);
    }

    public function getServices(): array
    {
        $data = $this->post(['action' => 'services']);
        return is_array($data) ? $data : [];
    }

    public function placeOrder(int $serviceId, string $link, int $quantity): int
    {
        $data = $this->post([
            'action'   => 'add',
            'service'  => $serviceId,
            'link'     => $link,
            'quantity' => $quantity,
        ]);
        if (empty($data['order'])) {
            throw new \Exception('JAP did not return an order ID.');
        }
        return (int) $data['order'];
    }

    public function getOrderStatus(int $orderId): array
    {
        $data = $this->post([
            'action' => 'status',
            'order'  => $orderId,
        ]);
        return $data;
    }

    public function getMultipleOrderStatuses(array $orderIds): array
    {
        $data = $this->post([
            'action' => 'status',
            'orders' => implode(',', $orderIds),
        ]);
        return is_array($data) ? $data : [];
    }
}
