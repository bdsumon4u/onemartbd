<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeItem;
use App\Models\Category;
use App\Models\Media;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeItem;
use App\Models\WebSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductForwardingController extends Controller
{
    public function receiveFromSlave(Request $request): JsonResponse
    {
        Log::info('Received forwarded product from slave', ['payload' => $request->all()]);

        if (! $this->isMasterSite()) {
            Log::warning('Ignoring forwarded product because this site is not configured as master');

            return response()->json([
                'message' => 'This site is not configured as a master.',
            ], 400);
        }

        $head = $request->validate([
            'slave_product_id' => ['required', 'integer'],
            'slave_domain' => ['required', 'string', 'max:255'],
            'deleted' => ['sometimes', 'boolean'],
        ]);

        $slaveProductId = (int) $head['slave_product_id'];
        $slaveDomain = (string) $head['slave_domain'];

        if ($request->boolean('deleted')) {
            $deleted = Product::query()
                ->where('slave_domain', $slaveDomain)
                ->where('slave_id', $slaveProductId)
                ->delete();

            Log::info('Master processed product deletion from slave', [
                'slave_domain' => $slaveDomain,
                'slave_product_id' => $slaveProductId,
                'rows_deleted' => $deleted,
            ]);

            return response()->json([
                'status' => 'ok',
                'deleted' => $deleted > 0,
            ]);
        }

        $data = $request->validate([
            'name' => ['required', 'string'],
            'slug' => ['required', 'string'],
            'sku' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'purchase_cost' => ['nullable'],
            'price' => ['nullable'],
            'sale_price' => ['nullable'],
            'stock' => ['nullable', 'integer'],
            'status' => ['required', 'integer'],
            'brand_name' => ['nullable', 'string'],
            'thumb' => ['nullable', 'string'],
            'image' => ['nullable', 'string'],
            'gallery' => ['nullable', 'array'],
            'categories' => ['nullable', 'array'],
            'attributes' => ['nullable', 'array'],
        ]);

        $existing = Product::query()
            ->where('slave_domain', $slaveDomain)
            ->where('slave_id', $slaveProductId)
            ->first();

        try {
            if ($existing) {
                DB::transaction(function () use ($existing, $data): void {
                    $updates = [
                        'name' => $data['name'],
                        'slug' => $data['slug'],
                        'sku' => $data['sku'] ?? null,
                        'description' => $data['description'] ?? null,
                        'purchase_cost' => $data['purchase_cost'] ?? null,
                        'price' => $data['price'] ?? null,
                        'sale_price' => $data['sale_price'] ?? null,
                        'stock' => $data['stock'] ?? null,
                        'status' => (int) $data['status'],
                        'brand_name' => $data['brand_name'] ?? null,
                    ];

                    $thumbId = $this->createMediaFromRemote($data['thumb'] ?? null, 2, 'forwarded_thumb');
                    if ($thumbId !== null) {
                        $updates['thumb'] = $thumbId;
                    }

                    $imageId = $this->createMediaFromRemote($data['image'] ?? null, 1, 'forwarded_image');
                    if ($imageId !== null) {
                        $updates['image'] = $imageId;
                    }

                    if (! empty($data['gallery']) && is_array($data['gallery'])) {
                        $galleryIds = [];
                        foreach ($data['gallery'] as $galleryUrl) {
                            $mediaId = $this->createMediaFromRemote(is_string($galleryUrl) ? $galleryUrl : null, 1, 'forwarded_gallery');
                            if ($mediaId !== null) {
                                $galleryIds[] = $mediaId;
                            }
                        }

                        if ($galleryIds !== []) {
                            $updates['gallery_images'] = implode(',', $galleryIds);
                        }
                    }

                    $existing->update($updates);

                    if (! empty($data['categories']) && is_array($data['categories'])) {
                        $this->syncCategories($existing, $data['categories']);
                    }

                    if (! empty($data['attributes']) && is_array($data['attributes'])) {
                        $this->syncAttributes($existing, $data['attributes']);
                    }
                });

                Log::info('Forwarded product updated on master', [
                    'master_product_id' => $existing->id,
                    'slave_domain' => $slaveDomain,
                    'slave_product_id' => $slaveProductId,
                ]);

                return response()->json([
                    'master_product_id' => $existing->id,
                    'status' => 'updated',
                ]);
            }

            // create new product on master
            $product = DB::transaction(function () use ($data, $slaveProductId, $slaveDomain) {
                $thumbId = $this->createMediaFromRemote($data['thumb'] ?? null, 2, 'forwarded_thumb');
                $imageId = $this->createMediaFromRemote($data['image'] ?? null, 1, 'forwarded_image');

                $galleryIds = [];
                if (! empty($data['gallery']) && is_array($data['gallery'])) {
                    foreach ($data['gallery'] as $galleryUrl) {
                        $mediaId = $this->createMediaFromRemote(is_string($galleryUrl) ? $galleryUrl : null, 1, 'forwarded_gallery');
                        if ($mediaId !== null) {
                            $galleryIds[] = $mediaId;
                        }
                    }
                }

                $product = Product::create([
                    'name' => $data['name'],
                    'slug' => $data['slug'],
                    'sku' => $data['sku'] ?? null,
                    'description' => $data['description'] ?? null,
                    'purchase_cost' => $data['purchase_cost'] ?? null,
                    'price' => $data['price'] ?? null,
                    'sale_price' => $data['sale_price'] ?? null,
                    'stock' => $data['stock'] ?? null,
                    'status' => (int) $data['status'],
                    'brand_name' => $data['brand_name'] ?? null,
                    'thumb' => $thumbId,
                    'image' => $imageId,
                    'gallery_images' => empty($galleryIds) ? null : implode(',', $galleryIds),
                    'slave_id' => $slaveProductId,
                    'slave_domain' => $slaveDomain,
                ]);

                if (! empty($data['categories']) && is_array($data['categories'])) {
                    $this->syncCategories($product, $data['categories']);
                }

                if (! empty($data['attributes']) && is_array($data['attributes'])) {
                    $this->syncAttributes($product, $data['attributes']);
                }

                return $product;
            });

            Log::info('Forwarded product created on master', [
                'master_product_id' => $product->id,
                'slave_domain' => $slaveDomain,
                'slave_product_id' => $slaveProductId,
            ]);

            return response()->json([
                'master_product_id' => $product->id,
                'status' => 'created',
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Failed to store forwarded product on master', [
                'slave_domain' => $slaveDomain,
                'slave_product_id' => $slaveProductId,
                'error' => $e->getMessage(),
            ]);

            report($e);

            return response()->json([
                'message' => 'Failed to create forwarded product on master.',
            ], 500);
        }
    }

    private function isMasterSite(): bool
    {
        /** @var WebSettings|null $settings */
        $settings = WebSettings::query()->find(1);

        $raw = $settings?->master_domain;
        if (! is_string($raw)) {
            return true;
        }

        return trim($raw) === '';
    }

    private function syncCategories(Product $product, array $rawCategories): void
    {
        $categoryIds = [];

        foreach ($rawCategories as $rawCategory) {
            $categoryName = '';
            $categoryImageUrl = null;

            if (is_array($rawCategory)) {
                $categoryName = trim((string) ($rawCategory['name'] ?? ''));
                $categoryImageUrl = is_string($rawCategory['image'] ?? null) ? (string) $rawCategory['image'] : null;
            } else {
                $categoryName = trim((string) $rawCategory);
            }

            if ($categoryName === '') {
                continue;
            }

            $category = Category::firstOrCreate(['category_name' => $categoryName], ['status' => 1]);

            $localCategoryImage = $this->downloadRemoteImage($categoryImageUrl, 'uploads/categories', 'forwarded_category');
            if ($localCategoryImage !== null) {
                $category->update(['image' => $localCategoryImage]);
            }

            $categoryIds[] = $category->id;
        }

        if ($categoryIds !== []) {
            $product->get_categories()->sync($categoryIds);
        }
    }

    private function syncAttributes(Product $product, array $rawAttributes): void
    {
        $existingAttributeIds = ProductAttribute::query()
            ->where('product_id', $product->id)
            ->pluck('id');

        if ($existingAttributeIds->isNotEmpty()) {
            ProductAttributeItem::query()->whereIn('product_attribute_id', $existingAttributeIds)->delete();
            ProductAttribute::query()->whereIn('id', $existingAttributeIds)->delete();
        }

        foreach ($rawAttributes as $attributeEntry) {
            if (! is_array($attributeEntry)) {
                continue;
            }

            $title = trim((string) ($attributeEntry['attribute'] ?? ''));
            if ($title === '') {
                continue;
            }

            $attribute = Attribute::firstOrCreate(['title' => $title], ['status' => 1]);

            $productAttribute = ProductAttribute::create([
                'product_id' => $product->id,
                'attribute_id' => $attribute->id,
            ]);

            $items = $attributeEntry['items'] ?? [];
            if (! is_array($items)) {
                continue;
            }

            foreach ($items as $itemTitle) {
                $itemTitle = trim((string) $itemTitle);
                if ($itemTitle === '') {
                    continue;
                }

                $attributeItem = AttributeItem::firstOrCreate([
                    'attribute_id' => $attribute->id,
                    'item_title' => $itemTitle,
                ]);

                ProductAttributeItem::create([
                    'product_attribute_id' => $productAttribute->id,
                    'product_attribute_item_id' => $attributeItem->id,
                ]);
            }
        }
    }

    private function createMediaFromRemote(?string $url, int $type, string $prefix): ?int
    {
        $localPath = $this->downloadRemoteImage($url, 'uploads', $prefix);
        if ($localPath === null) {
            return null;
        }

        $media = Media::create([
            'type' => $type,
            'file_original_name' => basename($localPath),
            'file_url' => $localPath,
            'user_id' => null,
        ]);

        return (int) $media->id;
    }

    private function downloadRemoteImage(?string $url, string $directory, string $prefix): ?string
    {
        if (! is_string($url)) {
            return null;
        }

        $url = trim($url);
        if (! $this->isHttpUrl($url)) {
            return null;
        }

        try {
            $request = Http::timeout(30)->accept('*/*')->retry(2, 200);
            if (app()->environment('local') || str_contains($url, '.test') || str_contains($url, 'localhost')) {
                $request = $request->withoutVerifying();
            }

            $response = $request->get($url);
            if (! $response->successful()) {
                Log::warning('Failed to download forwarded image', ['url' => $url, 'status' => $response->status()]);

                return null;
            }

            $extension = $this->resolveImageExtension($url, (string) $response->header('Content-Type'));
            $relativeDirectory = trim($directory, '/');
            $absoluteDirectory = public_path($relativeDirectory);
            if (! is_dir($absoluteDirectory)) {
                mkdir($absoluteDirectory, 0755, true);
            }

            $filename = Str::uuid()->toString().'_'.$prefix.'.'.$extension;
            $absolutePath = $absoluteDirectory.'/'.$filename;
            file_put_contents($absolutePath, $response->body());

            return $relativeDirectory.'/'.$filename;
        } catch (\Throwable $e) {
            Log::warning('Exception while downloading forwarded image', ['url' => $url, 'error' => $e->getMessage()]);

            return null;
        }
    }

    private function resolveImageExtension(string $url, string $contentType): string
    {
        $contentType = strtolower(trim(explode(';', $contentType)[0] ?? ''));

        $map = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            'image/svg+xml' => 'svg',
        ];

        if (isset($map[$contentType])) {
            return $map[$contentType];
        }

        $path = (string) parse_url($url, PHP_URL_PATH);
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if ($extension !== '' && preg_match('/^[a-z0-9]{2,5}$/', $extension) === 1) {
            return $extension;
        }

        return 'jpg';
    }

    private function isHttpUrl(string $url): bool
    {
        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        return $scheme === 'http' || $scheme === 'https';
    }
}
