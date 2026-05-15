<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\WebSettings;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrderForwardingService
{
    public function __construct(
        private ProductForwardingService $productForwardingService,
        private ?HttpFactory $http = null,
    ) {
        $this->http = $this->http ?? Http::getFacadeRoot();
    }

    public function forwardIfConfigured(Order $order, bool $force = false): void
    {
        $masterBaseUrl = $this->masterBaseUrl();
        if ($masterBaseUrl === null) {
            return;
        }

        if (! $force && $order->master_id !== null) {
            return;
        }

        $order->loadMissing('get_products.get_product', 'get_assigned');

        $payload = $this->buildForwardPayload($order);

        Log::info('Attempting to forward order to master', [
            'order_id' => $order->id,
            'invoice_id' => $order->invoice_id,
            'master_base_url' => $masterBaseUrl,
            'payload_summary' => [
                'slave_order_id' => $payload['slave_order_id'] ?? null,
                'items_count' => isset($payload['items']) ? count($payload['items']) : 0,
                'status' => $payload['status'] ?? null,
                'order_date' => $payload['order_date'] ?? null,
                'payment_status' => $payload['payment_status'] ?? null,
                'payment_method' => $payload['payment_method'] ?? null,
                'assigned_to' => $payload['assigned_to'] ?? null,
            ],
        ]);

        $order->forwarding_status = 'pending';
        $order->save();

        try {
            $url = $masterBaseUrl.'/api/slave-orders';

            /** @var Response $response */
            $response = $this->httpForUrl($url)->post($url, $payload);

            if (! $response->successful() && $response->status() === 422) {
                $missingProducts = $this->extractMissingProductsFromResponse($response);

                if ($missingProducts !== []) {
                    Log::warning('Order forwarding failed due to missing products on master. Forwarding products then retrying order.', [
                        'order_id' => $order->id,
                        'missing_products' => $missingProducts,
                    ]);

                    $this->forwardOrderProductsToMaster($order, true, $missingProducts);

                    /** @var Response $response */
                    $response = $this->httpForUrl($url)->post($url, $payload);
                }
            }

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

    /**
     * @return array<int,string>
     */
    private function extractMissingProductsFromResponse(Response $response): array
    {
        /** @var mixed $json */
        $json = $response->json();
        if (! is_array($json)) {
            return [];
        }

        $missing = $json['missing_products'] ?? null;
        if (! is_array($missing)) {
            return [];
        }

        return collect($missing)
            ->filter(fn ($name) => is_string($name) && trim($name) !== '')
            ->map(fn (string $name) => trim($name))
            ->values()
            ->all();
    }

    /**
     * @param  array<int,string>  $onlyProductNames
     */
    private function forwardOrderProductsToMaster(Order $order, bool $force, array $onlyProductNames = []): void
    {
        Log::info('Forwarding order products to master', [
            'order_id' => $order->id,
            'force' => $force,
            'only_product_names' => $onlyProductNames,
        ]);

        $targetNames = collect($onlyProductNames)
            ->map(fn (string $name) => mb_strtolower(trim($name)))
            ->filter()
            ->values()
            ->all();

        Log::debug('Normalized target product names for forwarding', [
            'target_names' => $targetNames,
        ]);

        foreach ($order->get_products as $item) {
            $product = $item->get_product;

            Log::debug('Processing order item for product forwarding', [
                'order_id' => $order->id,
                'item_id' => $item->id,
                'product_id' => $product?->id,
                'product_name' => $product?->name,
            ]);

            if (! $product instanceof Product) {
                Log::error('Order product item has no associated product', [
                    'order_id' => $order->id,
                    'item_id' => $item->id,
                ]);
                continue;
            }

            if ($targetNames !== [] && ! in_array(mb_strtolower(trim((string) $product->name)), $targetNames, true)) {
                Log::info('Skipping product not in missing products list', [
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                ]);
                continue;
            }

            if ($force) {
                $this->productForwardingService->retryForwarding($product);
            } else {
                $this->productForwardingService->forwardIfConfigured($product);
            }
        }
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
        $slaveDomain = request()->getHost() ?? parse_url(config('app.url', 'localhost'), PHP_URL_HOST);
        $assignedEmployeeId = optional($order->get_assigned)->employee_id;

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
            'ip_address' => $order->ip_address,
            'order_date' => $order->order_date,
            'payment_status' => $order->payment_status ?? 0,
            'payment_method' => $order->payment_method ?? 0,
            'order_notes' => $order->staff_note,
            'assigned_to' => is_numeric($assignedEmployeeId) ? (int) $assignedEmployeeId : null,
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
            'slave_domain' => request()->getHost() ?? parse_url(config('app.url', 'localhost'), PHP_URL_HOST),
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
