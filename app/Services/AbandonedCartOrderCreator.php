<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\AbandonedCart;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AbandonedCartOrderCreator
{
    public function __construct(
        private readonly OrderInvoiceIdGenerator $invoiceIdGenerator,
        private readonly OrderAssignmentService $orderAssignmentService,
    ) {}

    public function createFromAbandonedCart(AbandonedCart $cart): Order
    {
        return DB::transaction(function () use ($cart): Order {
            $customer = $this->getOrCreateCustomer($cart);

            $order = Order::query()->create([
                'invoice_id' => $this->invoiceIdGenerator->next(),
                'order_date' => now()->toDateString(),
                'customer_id' => $customer?->id,
                'customer_name' => $cart->customer_name ?? '',
                'customer_phone' => $cart->customer_phone ?? '',
                'customer_address' => $cart->customer_address ?? '',
                'shipping_cost' => $cart->shipping_cost ?? 0,
                'total' => $cart->total ?? 0,
                'status' => OrderStatus::Processing->value,
                'sub_total' => $cart->subtotal ?? 0,
                'discount' => $cart->discount ?? 0,
                'due' => $cart->total ?? 0,
                'courier_note' => $cart->note,
                'source' => 'incomplete',
            ]);

            if ($cart->employee_id) {
                $this->orderAssignmentService->assignNewOrderToEmployee((int) $order->id, (int) $cart->employee_id);
            }

            foreach ($this->items($cart) as $item) {
                OrderProduct::query()->create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'total' => $item['qty'] * $item['price'],
                    'attributes' => $item['attributes'] ?? null,
                    'attribute_ids' => $item['attribute_ids'] ?? null,
                ]);
            }

            $cart->delete();

            return $order;
        });
    }

    /**
     * @return array<int, array{product_id:int,qty:int,price:float|int,attributes?:string|null,attribute_ids?:string|null}>
     */
    private function items(AbandonedCart $cart): array
    {
        $decoded = json_decode((string) $cart->abandoned_item, true);

        if (! is_array($decoded)) {
            return [];
        }

        $items = [];
        foreach ($decoded as $item) {
            if (! is_array($item)) {
                continue;
            }

            $productId = $item['product_id'] ?? null;
            $qty = $item['qty'] ?? null;
            $price = $item['price'] ?? null;

            if (! is_numeric($productId) || ! is_numeric($qty) || ! is_numeric($price)) {
                continue;
            }

            $items[] = [
                'product_id' => (int) $productId,
                'qty' => (int) $qty,
                'price' => $price + 0,
                'attributes' => $item['attributes'] ?? null,
                'attribute_ids' => $item['attribute_ids'] ?? null,
            ];
        }

        return $items;
    }

    private function getOrCreateCustomer(AbandonedCart $cart): ?User
    {
        $phone = $cart->customer_phone ? trim((string) $cart->customer_phone) : '';

        if ($phone === '') {
            return null;
        }

        $existingUser = User::query()->where('phone', $phone)->first();

        if ($existingUser) {
            return $existingUser->status == 1 ? $existingUser : null;
        }

        return User::query()->create([
            'name' => $cart->customer_name ?? '',
            'phone' => $phone,
            'address' => $cart->customer_address ?? '',
            'password' => Hash::make($phone),
        ]);
    }
}
