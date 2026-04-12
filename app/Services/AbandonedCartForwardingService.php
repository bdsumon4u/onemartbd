<?php

namespace App\Services;

use App\Models\AbandonedCart;
use App\Models\Product;
use App\Models\WebSettings;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AbandonedCartForwardingService
{
    public function __construct(
        private ?HttpFactory $http = null,
    ) {
        $this->http = $this->http ?? Http::getFacadeRoot();
    }

    public function syncToMaster(AbandonedCart $cart): void
    {
        $masterBaseUrl = $this->masterBaseUrl();
        if ($masterBaseUrl === null) {
            return;
        }

        $items = $this->buildItemsPayload($cart);
        if ($items === []) {
            return;
        }

        $cart->forwarding_status = 'pending';
        $cart->save();

        $payload = [
            'slave_cart_id' => $cart->id,
            'slave_domain' => request()->getHost(),
            'customer_name' => $cart->customer_name,
            'customer_phone' => $cart->customer_phone,
            'customer_address' => $cart->customer_address,
            'shipping_cost' => $cart->shipping_cost,
            'subtotal' => $cart->subtotal,
            'total' => $cart->total,
            'discount' => $cart->discount !== null ? (float) $cart->discount : 0,
            'note' => $cart->note,
            'status' => (int) $cart->status,
            'items' => $items,
            'ip_address' => $cart->ip_address,
            'utm_source' => $cart->utm_source,
            'source' => $cart->source,
        ];

        Log::info('Syncing abandoned cart to master', [
            'abandoned_cart_id' => $cart->id,
            'master_base_url' => $masterBaseUrl,
            'items_count' => count($items),
        ]);

        try {
            $url = $masterBaseUrl.'/api/slave-abandoned-carts';

            /** @var Response $response */
            $response = $this->httpForUrl($url)->post($url, $payload);

            if (! $response->successful()) {
                $this->markForwardingFailed(
                    $cart,
                    'Non-success HTTP status when syncing abandoned cart: '.$response->status().' Body: '.mb_substr((string) $response->body(), 0, 500)
                );

                return;
            }

            /** @var array<string, mixed> $data */
            $data = $response->json() ?? [];
            $masterCartId = $data['master_abandoned_cart_id'] ?? null;

            if (is_numeric($masterCartId)) {
                $cart->master_id = (int) $masterCartId;
            }

            $cart->forwarding_status = 'success';
            $cart->forwarding_last_error = null;
            $cart->save();

            Log::info('Abandoned cart synced to master', [
                'abandoned_cart_id' => $cart->id,
                'master_id' => $cart->master_id,
            ]);
        } catch (\Throwable $e) {
            Log::error('Exception while syncing abandoned cart to master', [
                'abandoned_cart_id' => $cart->id,
                'error' => $e->getMessage(),
            ]);

            $this->markForwardingFailed(
                $cart,
                'Exception while syncing abandoned cart: '.$e->getMessage()
            );
        }
    }

    public function notifyMasterOfDeletion(AbandonedCart $cart): void
    {
        $masterBaseUrl = $this->masterBaseUrl();
        if ($masterBaseUrl === null) {
            return;
        }

        $payload = [
            'slave_cart_id' => $cart->id,
            'slave_domain' => request()->getHost(),
            'deleted' => true,
        ];

        $url = $masterBaseUrl.'/api/slave-abandoned-carts';

        Log::info('Notifying master of abandoned cart deletion', [
            'abandoned_cart_id' => $cart->id,
            'url' => $url,
        ]);

        try {
            $response = $this->httpForUrl($url)->post($url, $payload);

            if (! $response->successful()) {
                Log::error('Failed to notify master of abandoned cart deletion', [
                    'abandoned_cart_id' => $cart->id,
                    'http_status' => $response->status(),
                    'body' => mb_substr((string) $response->body(), 0, 500),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('Exception while notifying master of abandoned cart deletion', [
                'abandoned_cart_id' => $cart->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @return list<array{product_name: string, quantity: int, unit_price: float, attributes: mixed, attribute_ids: mixed}>
     */
    private function buildItemsPayload(AbandonedCart $cart): array
    {
        $decoded = json_decode((string) $cart->abandoned_item, true);

        if (! is_array($decoded)) {
            return [];
        }

        $items = [];

        foreach ($decoded as $row) {
            if (! is_array($row)) {
                continue;
            }

            $productId = $row['product_id'] ?? null;
            if (! is_numeric($productId)) {
                continue;
            }

            $product = Product::query()->find((int) $productId);
            if (! $product) {
                continue;
            }

            $items[] = [
                'product_name' => (string) $product->name,
                'quantity' => (int) ($row['qty'] ?? 0),
                'unit_price' => (float) ($row['price'] ?? 0),
                'attributes' => $row['attributes'] ?? null,
                'attribute_ids' => $row['attribute_ids'] ?? null,
            ];
        }

        return $items;
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

    private function markForwardingFailed(AbandonedCart $cart, string $reason): void
    {
        $cart->forwarding_status = 'failed';
        $cart->forwarding_last_error = mb_substr($reason, 0, 1000);
        $cart->save();
    }
}
