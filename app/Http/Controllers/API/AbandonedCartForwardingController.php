<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AbandonedCart;
use App\Models\Product;
use App\Models\WebSettings;
use App\Services\AbandonedCartEmployeeAssigner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AbandonedCartForwardingController extends Controller
{
    public function __construct(
        private AbandonedCartEmployeeAssigner $abandonedCartEmployeeAssigner,
    ) {}

    public function receiveFromSlave(Request $request): JsonResponse
    {
        Log::info('Received abandoned cart payload from slave', ['payload' => $request->all()]);

        if (! $this->isMasterSite()) {
            Log::warning('Ignoring forwarded abandoned cart because this site is not configured as master');

            return response()->json([
                'message' => 'This site is not configured as a master.',
            ], 400);
        }

        $head = $request->validate([
            'slave_cart_id' => ['required', 'integer'],
            'slave_domain' => ['required', 'string', 'max:255'],
            'deleted' => ['sometimes', 'boolean'],
        ]);

        $slaveCartId = (int) $head['slave_cart_id'];
        $slaveDomain = (string) $head['slave_domain'];

        if ($request->boolean('deleted')) {
            return $this->deleteForwardedCart($slaveDomain, $slaveCartId);
        }

        $data = $request->validate([
            'customer_name' => ['required', 'string'],
            'customer_phone' => ['nullable', 'string', 'max:191'],
            'customer_address' => ['nullable', 'string'],
            'shipping_cost' => ['required'],
            'subtotal' => ['required'],
            'total' => ['required'],
            'discount' => ['nullable'],
            'note' => ['nullable', 'string'],
            'status' => ['required', 'integer'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_name' => ['required', 'string'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required'],
            'items.*.attributes' => ['nullable'],
            'items.*.attribute_ids' => ['nullable'],
            'ip_address' => ['nullable', 'string', 'max:191'],
            'source' => ['nullable', 'string', 'max:191'],
            'utm_source' => ['nullable', 'string', 'max:150'],
        ]);

        $productNameToId = [];
        $missingProducts = [];

        foreach ($data['items'] as $item) {
            $name = trim((string) $item['product_name']);

            if ($name === '') {
                continue;
            }

            if (isset($productNameToId[$name])) {
                continue;
            }

            $product = Product::query()->where('name', $name)->first();
            if (! $product) {
                $missingProducts[] = $name;

                continue;
            }

            $productNameToId[$name] = (int) $product->id;
        }

        if ($missingProducts !== []) {
            Log::warning('Missing products on master for forwarded abandoned cart', [
                'slave_domain' => $slaveDomain,
                'slave_cart_id' => $slaveCartId,
                'missing_products' => $missingProducts,
            ]);

            return response()->json([
                'message' => 'Some products could not be matched on the master site.',
                'missing_products' => $missingProducts,
            ], 422);
        }

        $masterItems = [];

        foreach ($data['items'] as $item) {
            $name = trim((string) $item['product_name']);
            $productId = $productNameToId[$name] ?? null;

            if (! $productId) {
                continue;
            }

            $masterItems[] = [
                'product_id' => $productId,
                'qty' => (int) $item['quantity'],
                'price' => (float) $item['unit_price'],
                'attributes' => $item['attributes'] ?? null,
                'attribute_ids' => $item['attribute_ids'] ?? null,
            ];
        }

        if ($masterItems === []) {
            return response()->json([
                'message' => 'No valid line items after product matching.',
            ], 422);
        }

        $utmSource = $data['utm_source'] ?? null;
        if ($utmSource === null || $utmSource === '') {
            $utmSource = 'direct';
        }

        $assigner = $this->abandonedCartEmployeeAssigner;

        $existing = AbandonedCart::query()
            ->where('slave_domain', $slaveDomain)
            ->where('slave_id', $slaveCartId)
            ->first();

        if ($existing) {
            $existing->update([
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'] ?? null,
                'customer_address' => $data['customer_address'] ?? null,
                'shipping_cost' => (float) $data['shipping_cost'],
                'subtotal' => (float) $data['subtotal'],
                'total' => (float) $data['total'],
                'discount' => isset($data['discount']) ? (float) $data['discount'] : 0,
                'note' => $data['note'] ?? null,
                'status' => (int) $data['status'],
                'abandoned_item' => json_encode($masterItems),
                'ip_address' => $data['ip_address'] ?? null,
                'source' => $data['source'] ?? 'direct',
                'utm_source' => is_string($utmSource) ? strtolower($utmSource) : 'direct',
            ]);

            Log::info('Forwarded abandoned cart updated on master', [
                'master_abandoned_cart_id' => $existing->id,
                'slave_domain' => $slaveDomain,
                'slave_cart_id' => $slaveCartId,
            ]);

            return response()->json([
                'master_abandoned_cart_id' => $existing->id,
                'status' => 'updated',
            ]);
        }

        $cart = AbandonedCart::query()->create([
            'customer_name' => $data['customer_name'],
            'customer_phone' => $data['customer_phone'] ?? null,
            'customer_address' => $data['customer_address'] ?? null,
            'shipping_cost' => (float) $data['shipping_cost'],
            'subtotal' => (float) $data['subtotal'],
            'total' => (float) $data['total'],
            'discount' => isset($data['discount']) ? (float) $data['discount'] : 0,
            'note' => $data['note'] ?? null,
            'status' => (int) $data['status'],
            'abandoned_item' => json_encode($masterItems),
            'ip_address' => $data['ip_address'] ?? null,
            'source' => $data['source'] ?? 'direct',
            'utm_source' => is_string($utmSource) ? strtolower($utmSource) : 'direct',
            'slave_id' => $slaveCartId,
            'slave_domain' => $slaveDomain,
        ]);

        $assigner->assignEmployeeToAbandonedCart($cart);

        Log::info('Forwarded abandoned cart created on master', [
            'master_abandoned_cart_id' => $cart->id,
            'slave_domain' => $slaveDomain,
            'slave_cart_id' => $slaveCartId,
        ]);

        return response()->json([
            'master_abandoned_cart_id' => $cart->id,
            'status' => 'created',
        ], 201);
    }

    private function deleteForwardedCart(string $slaveDomain, int $slaveCartId): JsonResponse
    {
        $deleted = AbandonedCart::query()
            ->where('slave_domain', $slaveDomain)
            ->where('slave_id', $slaveCartId)
            ->delete();

        Log::info('Master processed abandoned cart deletion from slave', [
            'slave_domain' => $slaveDomain,
            'slave_cart_id' => $slaveCartId,
            'rows_deleted' => $deleted,
        ]);

        return response()->json([
            'status' => 'ok',
            'deleted' => $deleted > 0,
        ]);
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
}
