<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\WebSettings;
use App\Services\OrderInvoiceIdGenerator;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderForwardingController extends Controller
{
    public function __construct(private OrderInvoiceIdGenerator $invoiceIdGenerator)
    {
    }

    public function receiveFromSlave(Request $request): JsonResponse
    {
        Log::info('Received forwarded order from slave', ['payload' => $request->all()]);

        if (! $this->isMasterSite()) {
            Log::warning('Ignoring forwarded order because this site is not configured as master');

            return response()->json([
                'message' => 'This site is not configured as a master.',
            ], 400);
        }

        $data = $request->validate([
            'slave_order_id' => ['required', 'integer'],
            'slave_domain' => ['required', 'string', 'max:255'],
            'status' => ['required', 'integer'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_name' => ['required', 'string'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required'],
            'customer' => ['required', 'array'],
            'customer.name' => ['required', 'string'],
            'customer.phone' => ['nullable', 'string', 'max:191'],
            'customer.email' => ['nullable', 'string', 'max:191'],
            'customer.address' => ['nullable', 'string'],
            'totals' => ['required', 'array'],
            'totals.subtotal' => ['required'],
            'totals.shipping' => ['required'],
            'totals.discount' => ['required'],
            'totals.grand_total' => ['required'],
        ]);

        $slaveOrderId = (int) $data['slave_order_id'];
        $slaveDomain = (string) $data['slave_domain'];

        $existing = Order::query()
            ->where('slave_domain', $slaveDomain)
            ->where('slave_id', $slaveOrderId)
            ->first();

        if ($existing) {
            Log::info('Forwarded order already exists on master', [
                'slave_domain' => $slaveDomain,
                'slave_order_id' => $slaveOrderId,
                'master_order_id' => $existing->id,
            ]);

            return response()->json([
                'master_order_id' => $existing->id,
                'status' => 'already_exists',
            ]);
        }

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
            Log::warning('Missing products on master for forwarded order', [
                'slave_domain' => $slaveDomain,
                'slave_order_id' => $slaveOrderId,
                'missing_products' => $missingProducts,
            ]);

            return response()->json([
                'message' => 'Some products could not be matched on the master site.',
                'missing_products' => $missingProducts,
            ], 422);
        }

        try {
            $order = DB::transaction(function () use ($data, $slaveOrderId, $slaveDomain, $productNameToId): Order {
                $customerData = $data['customer'];
                $totals = $data['totals'];

                $phone = isset($customerData['phone']) ? trim((string) $customerData['phone']) : '';

                $customer = null;
                if ($phone !== '') {
                    $customer = User::query()->where('phone', $phone)->first();
                }

                if (! $customer) {
                    $customer = User::query()->create([
                        'name' => $customerData['name'],
                        'phone' => $phone !== '' ? $phone : null,
                        'address' => $customerData['address'] ?? null,
                        'password' => bcrypt($phone !== '' ? $phone : 'secret'),
                    ]);
                } else {
                    $updates = [];
                    if (! $customer->name && $customerData['name'] !== '') {
                        $updates['name'] = $customerData['name'];
                    }
                    if (! $customer->address && ! empty($customerData['address'])) {
                        $updates['address'] = $customerData['address'];
                    }

                    if ($updates !== []) {
                        $customer->update($updates);
                    }
                }

                $subtotal = (float) $totals['subtotal'];
                $shipping = (float) $totals['shipping'];
                $discount = (float) $totals['discount'];
                $grandTotal = (float) $totals['grand_total'];

                $order = Order::query()->create([
                    'invoice_id' => $this->invoiceIdGenerator->next(),
                    'customer_id' => $customer->id,
                    'customer_name' => $customerData['name'],
                    'customer_phone' => $customerData['phone'] ?? null,
                    'customer_email' => $customerData['email'] ?? null,
                    'customer_address' => $customerData['address'] ?? null,
                    'sub_total' => $subtotal,
                    'shipping_cost' => $shipping,
                    'discount' => $discount,
                    'total' => $grandTotal,
                    'paid' => 0,
                    'due' => $grandTotal,
                    'status' => (int) $data['status'],
                    'order_date' => now()->toDateString(),
                    'source' => $data['source'] ?? 'direct',
                    'utm_source' => $data['utm_source'] ?? 'direct',
                    'slave_id' => $slaveOrderId,
                    'slave_domain' => $slaveDomain,
                ]);

                foreach ($data['items'] as $item) {
                    $name = trim((string) $item['product_name']);
                    $productId = $productNameToId[$name] ?? null;

                    if (! $productId) {
                        continue;
                    }

                    $quantity = (int) $item['quantity'];
                    $unitPrice = (float) $item['unit_price'];

                    $purchaseCost = Product::query()->where('id', $productId)->value('purchase_cost') ?? 0;

                    $order->products()->create([
                        'product_id' => $productId,
                        'qty' => $quantity,
                        'price' => $unitPrice,
                        'purchase_cost' => $purchaseCost,
                    ]);
                }

                return $order;
            });

            Log::info('Forwarded order stored on master', [
                'master_order_id' => $order->id,
                'slave_domain' => $slaveDomain,
                'slave_order_id' => $slaveOrderId,
            ]);

            return response()->json([
                'master_order_id' => $order->id,
                'status' => 'success',
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Failed to store forwarded order on master', [
                'slave_domain' => $slaveDomain,
                'slave_order_id' => $slaveOrderId,
                'error' => $e->getMessage(),
            ]);

            report($e);

            return response()->json([
                'message' => 'Failed to create forwarded order on master.',
            ], 500);
        }
    }

    public function updateStatusFromSlave(Request $request): JsonResponse
    {
        Log::info('Received status update from slave', ['payload' => $request->all()]);

        if (! $this->isMasterSite()) {
            return response()->json([
                'message' => 'This site is not configured as a master.',
            ], 400);
        }

        $data = $request->validate([
            'slave_order_id' => ['required', 'integer'],
            'slave_domain' => ['required', 'string', 'max:255'],
            'status' => ['required', 'integer'],
        ]);

        $slaveOrderId = (int) $data['slave_order_id'];
        $slaveDomain = (string) $data['slave_domain'];

        /** @var Order|null $order */
        $order = Order::query()
            ->where('slave_domain', $slaveDomain)
            ->where('slave_id', $slaveOrderId)
            ->first();

        if (! $order) {
            Log::error('Master could not find order for status update from slave', [
                'slave_domain' => $slaveDomain,
                'slave_order_id' => $slaveOrderId,
            ]);

            return response()->json([
                'message' => 'Order not found on master for given slave reference.',
            ], 404);
        }

        DB::transaction(function () use ($order, $data): void {
            Order::withoutEvents(function () use ($order, $data): void {
                $order->update([
                    'status' => (int) $data['status'],
                ]);
            });
        });

        Log::info('Master updated order status from slave', [
            'master_order_id' => $order->id,
            'slave_domain' => $slaveDomain,
            'slave_order_id' => $slaveOrderId,
            'status' => $data['status'],
        ]);

        return response()->json([
            'status' => 'ok',
        ]);
    }

    public function updateStatusFromMaster(Request $request): JsonResponse
    {
        Log::info('Received status update from master', ['payload' => $request->all()]);

        $data = $request->validate([
            'master_order_id' => ['required', 'integer'],
            'slave_order_id' => ['required', 'integer'],
            'status' => ['required', 'integer'],
        ]);

        $slaveOrderId = (int) $data['slave_order_id'];
        $masterOrderId = (int) $data['master_order_id'];

        /** @var Order|null $order */
        $order = Order::query()
            ->where('id', $slaveOrderId)
            ->first();

        if (! $order) {
            Log::error('Slave could not find order for status update from master', [
                'slave_order_id' => $slaveOrderId,
                'master_order_id' => $masterOrderId,
            ]);

            return response()->json([
                'message' => 'Order not found on slave for given reference.',
            ], 404);
        }

        if ($order->master_id !== null && (int) $order->master_id !== $masterOrderId) {
            Log::warning('Slave received status update with mismatched master_id', [
                'slave_order_id' => $slaveOrderId,
                'expected_master_id' => $order->master_id,
                'payload_master_id' => $masterOrderId,
            ]);
        }

        DB::transaction(function () use ($order, $data): void {
            Order::withoutEvents(function () use ($order, $data): void {
                $order->update([
                    'status' => (int) $data['status'],
                    'forwarding_status' => 'success',
                    'forwarding_last_error' => null,
                ]);
            });
        });

        Log::info('Slave updated local order status from master', [
            'slave_order_id' => $order->id,
            'master_order_id' => $masterOrderId,
            'status' => $data['status'],
        ]);

        return response()->json([
            'status' => 'ok',
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
