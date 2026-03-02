<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\WebSettings;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrderForwardingService
{
    public function __construct(
        private ?HttpFactory $http = null,
    ) {
        $this->http = $this->http ?? Http::getFacadeRoot();
    }

    public function forwardIfConfigured(Order $order, bool $force = false): void
    {
        info('forwardIfConfigured', ['order' => $order]);
        $masterBaseUrl = $this->masterBaseUrl();
        if ($masterBaseUrl === null) {
            info('masterBaseUrl is null');
            return;
        }

        if (! $force && $order->master_id !== null) {
            info('order already has a master_id');
            return;
        }

        $order->loadMissing('get_products.get_product');

        $payload = $this->buildForwardPayload($order);

        Log::info('Attempting to forward order to master', [
            'order_id' => $order->id,
            'invoice_id' => $order->invoice_id,
            'master_base_url' => $masterBaseUrl,
            'payload_summary' => [
                'slave_order_id' => $payload['slave_order_id'] ?? null,
                'items_count' => isset($payload['items']) ? count($payload['items']) : 0,
                'status' => $payload['status'] ?? null,
            ],
        ]);

        $order->forwarding_status = 'pending';
        $order->save();

        try {
            $url = $masterBaseUrl.'/api/slave-orders';

            /** @var Response $response */
            $response = $this->httpForUrl($url)->post($url, $payload);

            if (! $response->successful()) {
                $bodySnippet = mb_substr((string) $response->body(), 0, 500);

                $this->markForwardingFailed(
                    $order,
                    'Non-success HTTP status when forwarding: '.$response->status().' Body: '.$bodySnippet
                );

                return;
            }

            /** @var array<string,mixed> $data */
            $data = $response->json() ?? [];
            $masterOrderId = $data['master_order_id'] ?? null;

            if (is_numeric($masterOrderId)) {
                $order->master_id = (int) $masterOrderId;
            }

            $order->forwarding_status = 'success';
            $order->forwarding_last_error = null;
            $order->save();

            Log::info('Order forwarded successfully to master', [
                'order_id' => $order->id,
                'master_id' => $order->master_id,
                'slave_domain' => $payload['slave_domain'] ?? null,
            ]);
        } catch (\Throwable $e) {
            Log::error('Exception while forwarding order to master', [
                'order_id' => $order->id,
                'invoice_id' => $order->invoice_id,
                'error' => $e->getMessage(),
            ]);

            $this->markForwardingFailed(
                $order,
                'Exception while forwarding: '.$e->getMessage()
            );
        }
    }

    public function retryForwarding(Order $order): void
    {
        $this->forwardIfConfigured($order, true);
    }

    public function handleOrderStatusChanged(Order $order, int $oldStatus, int $newStatus): void
    {
        if ($oldStatus === $newStatus) {
            return;
        }

        $masterBaseUrl = $this->masterBaseUrl();
        $isSlaveSite = $masterBaseUrl !== null;

        if ($isSlaveSite && $order->master_id !== null) {
            $this->sendStatusToMaster($order, $masterBaseUrl);
        }

        if (! $isSlaveSite && $order->slave_id !== null && $order->slave_domain) {
            $this->sendStatusToSlave($order);
        }
    }

    private function buildForwardPayload(Order $order): array
    {
        $slaveDomain = request()->getHost();

        $items = [];
        foreach ($order->get_products as $item) {
            $product = $item->get_product;

            if (! $product instanceof Product) {
                continue;
            }

            $items[] = [
                'product_name' => $product->name,
                'quantity' => (int) $item->qty,
                'unit_price' => $item->price,
            ];
        }

        return [
            'slave_order_id' => $order->id,
            'slave_domain' => $slaveDomain,
            'status' => (int) $order->status,
            'source' => $order->source,
            'utm_source' => $order->utm_source,
            'items' => $items,
            'customer' => [
                'name' => $order->customer_name,
                'phone' => $order->customer_phone,
                'email' => $order->customer_email,
                'address' => $order->customer_address,
            ],
            'totals' => [
                'subtotal' => $order->sub_total,
                'shipping' => $order->shipping_cost,
                'discount' => $order->discount,
                'grand_total' => $order->total,
            ],
        ];
    }

    private function sendStatusToMaster(Order $order, string $masterBaseUrl): void
    {
        $payload = [
            'slave_order_id' => $order->id,
            'slave_domain' => request()->getHost(),
            'status' => (int) $order->status,
        ];

        $url = $masterBaseUrl.'/api/slave-orders/status';

        Log::info('Syncing order status to master', [
            'order_id' => $order->id,
            'master_id' => $order->master_id,
            'url' => $url,
            'status' => $payload['status'],
        ]);

        try {
            $response = $this->httpForUrl($url)->post($url, $payload);

            if (! $response->successful()) {
                Log::error('Failed to sync status to master', [
                    'order_id' => $order->id,
                    'http_status' => $response->status(),
                    'body' => mb_substr((string) $response->body(), 0, 500),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Exception while syncing status to master', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function sendStatusToSlave(Order $order): void
    {
        $slaveDomain = trim((string) $order->slave_domain);
        if ($slaveDomain === '') {
            return;
        }

        $baseUrl = $this->normalizeDomainToBaseUrl($slaveDomain);
        $url = $baseUrl.'/api/master-orders/status';

        $payload = [
            'master_order_id' => $order->id,
            'slave_order_id' => $order->slave_id,
            'status' => (int) $order->status,
        ];

        Log::info('Syncing order status to slave', [
            'order_id' => $order->id,
            'slave_id' => $order->slave_id,
            'slave_domain' => $slaveDomain,
            'url' => $url,
            'status' => $payload['status'],
        ]);

        try {
            $response = $this->httpForUrl($url)->post($url, $payload);

            if (! $response->successful()) {
                Log::error('Failed to sync status to slave', [
                    'order_id' => $order->id,
                    'http_status' => $response->status(),
                    'body' => mb_substr((string) $response->body(), 0, 500),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Exception while syncing status to slave', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function masterBaseUrl(): ?string
    {
        /** @var WebSettings|null $settings */
        $settings = WebSettings::query()->find(1);
        $raw = $settings?->master_domain;

        if (! is_string($raw)) {
            return null;
        }

        $raw = trim($raw);
        if ($raw === '') {
            return null;
        }

        return $this->normalizeDomainToBaseUrl($raw);
    }

    private function httpForUrl(string $url): PendingRequest
    {
        $http = $this->http
            ->acceptJson()
            ->asJson()
            ->withOptions([
                'allow_redirects' => [
                    'max' => 5,
                    'strict' => true,
                    'referer' => true,
                    'track_redirects' => true,
                ],
            ]);

        if (app()->environment('local') || str_contains($url, '.test') || str_contains($url, 'localhost')) {
            $http = $http->withoutVerifying();
        }

        return $http;
    }

    private function normalizeDomainToBaseUrl(string $domain): string
    {
        $base = trim($domain);

        if (! str_starts_with($base, 'http://') && ! str_starts_with($base, 'https://')) {
            $base = 'https://'.$base;
        }

        return rtrim($base, '/');
    }

    private function markForwardingFailed(Order $order, string $reason): void
    {
        $order->forwarding_status = 'failed';
        $order->forwarding_last_error = mb_substr($reason, 0, 1000);
        $order->save();
    }
}
