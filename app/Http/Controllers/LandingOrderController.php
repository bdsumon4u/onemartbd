<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Landing\StoreLandingOrderWebhookRequest;
use App\Models\Employee;
use App\Models\Order;
use App\Models\OrderAssign;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\SmsSetting;
use App\Models\User;
use App\Services\OrderForwardingService;
use App\Services\WhatsappServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class LandingOrderController extends Controller
{
    public function __invoke(
        StoreLandingOrderWebhookRequest $request,
        WhatsappServices $WpServices,
        OrderForwardingService $orderForwardingService
    ): JsonResponse {
        $payload = $request->validated();
        info('Landing Order Payload:', $payload);

        $sourceHref = (string) data_get($payload, '_links.self.0.href');
        $domain = $this->extractDomainFromUrl($sourceHref);

        /** @var array<string, mixed> $billing */
        $billing = $payload['billing'] ?? [];

        $name = trim(implode(' ', array_filter([
            (string) ($billing['first_name'] ?? ''),
            (string) ($billing['last_name'] ?? ''),
        ])));

        $addressParts = array_values(array_filter([
            (string) ($billing['address_1'] ?? ''),
            (string) ($billing['address_2'] ?? ''),
            (string) ($billing['city'] ?? ''),
        ]));
        $address = trim(implode(', ', $addressParts));

        $ipAddress = (string) ($payload['customer_ip_address'] ?? $request->ip());

        /** @var array<int, array<string, mixed>> $lineItems */
        $lineItems = $payload['line_items'] ?? [];

        $lineItemNameToProductId = [];
        $missingProducts = [];

        foreach ($lineItems as $item) {
            $resolvedProductId = $this->resolveLocalProductIdFromLineItem($item, $lineItemNameToProductId);

            if ($resolvedProductId === null) {
                $missingProducts[] = [
                    'name' => (string) ($item['name'] ?? ''),
                    'sku' => (string) ($item['sku'] ?? ''),
                ];
            } else {
                $itemName = trim((string) ($item['name'] ?? ''));
                if ($itemName !== '') {
                    $lineItemNameToProductId[$this->normalizeProductKey($itemName)] = $resolvedProductId;
                }
            }
        }

        if ($missingProducts !== []) {
            throw ValidationException::withMessages([
                'line_items' => ['Some products could not be matched to local products.'],
                'missing_products' => [json_encode($missingProducts, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)],
            ]);
        }

        $computedSubTotal = 0.0;
        foreach ($lineItems as $item) {
            $quantity = max(1, (int) ($item['quantity'] ?? 1));

            $lineTotalValue = $item['total'] ?? $item['subtotal'] ?? null;
            if ($lineTotalValue !== null) {
                $computedSubTotal += $this->normalizeMoney($lineTotalValue);

                continue;
            }

            $unitPrice = $this->normalizeMoney($item['price'] ?? 0);
            $computedSubTotal += $unitPrice * $quantity;
        }

        $shippingCost = $this->normalizeMoney($payload['shipping_total'] ?? 0);
        $discount = $this->normalizeMoney($payload['discount_total'] ?? 0);
        $total = $this->normalizeMoney($payload['total'] ?? ($computedSubTotal + $shippingCost));
        $subTotal = $computedSubTotal > 0 ? $computedSubTotal : max(0, $total - $shippingCost);

        /** @var Order $order */
        $order = DB::transaction(function () use (
            $name,
            $address,
            $billing,
            $ipAddress,
            $shippingCost,
            $subTotal,
            $total,
            $discount,
            $lineItems,
            $domain,
            $lineItemNameToProductId,
            $WpServices
        ) {
            if (Order::withTrashed()->count() > 0) {
                $invoice_id = Order::withTrashed()->latest('id')->first()->invoice_id;
                $invoice_id = trim($invoice_id, 'INV');
                $invoice_id++;
                $invoice_id = 'INV'.$invoice_id;
            } else {
                $invoice_id = 'INV1';
            }

            // create customer account
            $phone = trim((string) ($billing['phone'] ?? '')) ?: null;
            $check_cus = User::where('phone', $phone)->first();
            if ($check_cus) {
                $customer_id = $check_cus;
            } else {
                $customer_id = User::create([
                    'name' => $name,
                    'phone' => $phone,
                    'address' => $address,
                    'password' => Hash::make($phone),
                ]);
            }

            $order_date = now()->format('Y-m-d');

            $order_id = Order::create([
                'invoice_id' => $invoice_id,
                'order_date' => $order_date,
                'customer_id' => $customer_id->id,
                'ip_address' => $ipAddress,
                'source' => $domain,
                'status' => 2,
                'customer_name' => $name,
                'customer_phone' => $phone,
                'customer_address' => $address,
                'shipping_cost' => $this->formatMoney($shippingCost),
                'discount' => $this->formatMoney($discount),
                'sub_total' => $this->formatMoney($subTotal),
                'total' => $this->formatMoney($total),
            ]);

            $sms = SmsSetting::where('status', $order_id->status)->first();
            // send whatsapp
            if ($sms && $sms->is_whatsapp == 1 && $sms->template_name != null) {
                $WpServices->sendOrderWhatsapp($order_id, $sms->template_name, $sms->status);
            }

            foreach ($lineItems as $item) {
                $itemName = trim((string) ($item['name'] ?? ''));
                $normalizedKey = $this->normalizeProductKey($itemName);
                $localProductId = $lineItemNameToProductId[$normalizedKey] ?? null;

                if (! is_int($localProductId)) {
                    throw ValidationException::withMessages([
                        'line_items' => ['Product mapping failed while creating carts.'],
                        'name' => [$itemName],
                    ]);
                }

                $quantity = max(1, (int) ($item['quantity'] ?? 1));

                $unitPrice = $this->normalizeMoney($item['price'] ?? 0);
                if ($unitPrice <= 0) {
                    $lineTotal = $this->normalizeMoney($item['total'] ?? $item['subtotal'] ?? 0);
                    $unitPrice = $quantity > 0 ? ($lineTotal / $quantity) : 0.0;
                }

                $purchase_cost = Product::where('id', $localProductId)->first()->purchase_cost;
                OrderProduct::create([
                    'order_id' => $order_id->id,
                    'product_id' => $localProductId,
                    'qty' => $quantity,
                    'price' => (int) round($unitPrice),
                    'purchase_cost' => $purchase_cost,
                ]);
            }

            OrderAssign::create([
                'order_id' => $order_id->id,
                'employee_id' => Employee::where('status', 1)->inRandomOrder()->first()->id,
            ]);

            return $order_id;
        });

        $orderForwardingService->forwardIfConfigured($order);

        return response()->json([
            'success' => true,
            'order_id' => $order->id,
            'order_type' => $domain,
            'source' => $sourceHref,
        ], 201);
    }

    private function extractDomainFromUrl(string $url): string
    {
        $host = parse_url($url, PHP_URL_HOST);
        $host = is_string($host) ? trim($host) : '';

        if ($host === '') {
            return 'Landing';
        }

        return $host;
    }

    /**
     * @param  array<string, mixed>  $item
     * @param  array<string, int>  $cache
     */
    private function resolveLocalProductIdFromLineItem(array $item, array $cache): ?int
    {
        $name = trim((string) ($item['name'] ?? ''));
        $sku = trim((string) ($item['sku'] ?? ''));

        if ($name !== '') {
            $cacheKey = $this->normalizeProductKey($name);
            if (isset($cache[$cacheKey])) {
                return $cache[$cacheKey];
            }
        }

        if ($sku !== '') {
            $product = Product::query()->where('sku', $sku)->first();
            if ($product) {
                return (int) $product->id;
            }
        }

        if ($name === '') {
            return null;
        }

        $slug = Str::slug($name);

        $product = Product::query()
            ->where('name', $name)
            ->orWhere('slug', $slug)
            ->first();

        if ($product) {
            return (int) $product->id;
        }

        $lower = mb_strtolower($name);

        $product = Product::query()
            ->whereRaw('LOWER(name) = ?', [$lower])
            ->first();

        if ($product) {
            return (int) $product->id;
        }

        $safeLike = addcslashes($name, '%_\\');

        $product = Product::query()
            ->where('name', 'like', '%'.$safeLike.'%')
            ->first();

        return $product ? (int) $product->id : null;
    }

    private function normalizeProductKey(string $name): string
    {
        return mb_strtolower(trim($name));
    }

    private function normalizeMoney($value): float
    {
        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        $string = is_string($value) ? $value : (string) $value;
        $string = preg_replace('~[^0-9.\-]~', '', $string) ?? '';

        return (float) $string;
    }

    private function formatMoney(float $amount): string
    {
        return number_format($amount, 2, '.', '');
    }

    /**
     * @param  mixed  $metaData
     * @return array{0: ?string, 1: ?string, 2: ?string}
     */
    private function extractCartOptionsFromMeta($metaData): array
    {
        if (! is_array($metaData)) {
            return [null, null, null];
        }

        $color = null;
        $size = null;
        $model = null;

        foreach ($metaData as $entry) {
            if (! is_array($entry)) {
                continue;
            }

            $key = strtolower((string) ($entry['key'] ?? ''));
            $value = $entry['value'] ?? null;
            $valueString = is_string($value) ? trim($value) : null;

            if ($valueString === null || $valueString === '') {
                continue;
            }

            if (in_array($key, ['color', 'pa_color', 'attribute_pa_color'], true)) {
                $color = $valueString;
            }

            if (in_array($key, ['size', 'pa_size', 'attribute_pa_size'], true)) {
                $size = $valueString;
            }

            if (in_array($key, ['model', 'pa_model', 'attribute_pa_model'], true)) {
                $model = $valueString;
            }
        }

        return [$color, $size, $model];
    }
}
