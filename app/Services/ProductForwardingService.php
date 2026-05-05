<?php

namespace App\Services;

use App\Models\Product;
use App\Models\WebSettings;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProductForwardingService
{
    public function __construct(
        private ?HttpFactory $http = null,
    ) {
        $this->http = $this->http ?? Http::getFacadeRoot();
    }

    public function forwardIfConfigured(Product $product, bool $force = false): void
    {
        $masterBaseUrl = $this->masterBaseUrl();
        if ($masterBaseUrl === null) {
            return;
        }

        if (! $force && $product->master_id !== null) {
            return;
        }

        $product->loadMissing('get_categories', 'get_attributes.get_attribute.get_items', 'get_thumb', 'get_image');

        $payload = $this->buildForwardPayload($product);

        Log::info('Attempting to forward product to master', [
            'product_id' => $product->id,
            'slug' => $product->slug,
            'master_base_url' => $masterBaseUrl,
            'payload_summary' => [
                'slave_product_id' => $payload['slave_product_id'] ?? null,
                'categories' => isset($payload['categories']) ? count($payload['categories']) : 0,
            ],
        ]);

        $product->forwarding_status = 'pending';
        $product->save();

        try {
            $url = $masterBaseUrl.'/api/slave-products';

            /** @var Response $response */
            $response = $this->httpForUrl($url)->post($url, $payload);

            if (! $response->successful()) {
                $bodySnippet = mb_substr((string) $response->body(), 0, 500);

                $this->markForwardingFailed(
                    $product,
                    'Non-success HTTP status when forwarding: '.$response->status().' Body: '.$bodySnippet
                );

                return;
            }

            /** @var array<string,mixed> $data */
            $data = $response->json() ?? [];
            $masterProductId = $data['master_product_id'] ?? null;

            if (is_numeric($masterProductId)) {
                $product->master_id = (int) $masterProductId;
            }

            $product->forwarding_status = 'success';
            $product->forwarding_last_error = null;
            $product->save();

            Log::info('Product forwarded successfully to master', [
                'product_id' => $product->id,
                'master_id' => $product->master_id,
            ]);
        } catch (\Throwable $e) {
            Log::error('Exception while forwarding product to master', [
                'product_id' => $product->id,
                'slug' => $product->slug,
                'error' => $e->getMessage(),
            ]);

            $this->markForwardingFailed(
                $product,
                'Exception while forwarding: '.$e->getMessage()
            );
        }
    }

    public function retryForwarding(Product $product): void
    {
        $this->forwardIfConfigured($product, true);
    }

    private function buildForwardPayload(Product $product): array
    {
        $slaveDomain = parse_url(config('app.url', 'localhost'), PHP_URL_HOST);

        $categories = $product->get_categories->map(function ($category): array {
            return [
                'name' => $category->category_name,
                'image' => $this->absoluteAssetUrl($category->image),
            ];
        })->filter(fn (array $category): bool => (string) ($category['name'] ?? '') !== '')->values()->all();

        $attributes = [];
        foreach ($product->get_attributes as $pa) {
            $attribute = $pa->get_attribute;
            if (! $attribute) {
                continue;
            }

            $items = $pa->get_attribute_items->map(fn ($it) => $it->attributeItem?->item_title)->filter()->values()->all();

            $attributes[] = [
                'attribute' => $attribute->title,
                'items' => $items,
            ];
        }

        $thumbUrl = $this->absoluteAssetUrl($product->get_thumb?->file_url);
        $imageUrl = $this->absoluteAssetUrl($product->get_image?->file_url);

        $gallery = collect($product->images ?: [])->map(fn ($path) => $this->absoluteAssetUrl(is_string($path) ? $path : null))->filter()->values()->all();

        return [
            'slave_product_id' => $product->id,
            'slave_domain' => $slaveDomain,
            'name' => $product->name,
            'slug' => $product->slug,
            'sku' => $product->sku,
            'description' => $product->description,
            'purchase_cost' => $product->purchase_cost,
            'price' => $product->price,
            'sale_price' => $product->sale_price,
            'stock' => $product->stock,
            'status' => (int) $product->status,
            'brand_name' => $product->brand_name,
            'thumb' => $thumbUrl,
            'image' => $imageUrl,
            'gallery' => $gallery,
            'categories' => $categories,
            'attributes' => $attributes,
        ];
    }

    private function absoluteAssetUrl(?string $path): ?string
    {
        if (! is_string($path)) {
            return null;
        }

        $path = trim($path);
        if ($path === '') {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return url(ltrim($path, '/'));
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

    private function markForwardingFailed(Product $product, string $reason): void
    {
        $product->forwarding_status = 'failed';
        $product->forwarding_last_error = mb_substr($reason, 0, 1000);
        $product->save();
    }
}
