<?php

namespace App\Services;

use App\Models\Order;

class QuantityMonitorService
{
    public function updateOrderedQuantity(Order $order): void
    {
        $order->forceFill([
            'ordered_quantity' => $this->getOrderedQuantity($order),
        ])->saveQuietly();
    }

    public function getOrderedQuantity(Order $order): int
    {
        return (int) $order->products()->sum('qty');
    }

    public function updateDeliveredQuantity(Order $order, bool $force = false): void
    {
        if (! $force && ! is_null($order->delivered_quantity) && ! is_null($order->delivered_at)) {
            return;
        }

        $order->forceFill([
            'delivered_quantity' => (int) $order->products()->sum('qty'),
            'delivered_at' => now(),
        ])->saveQuietly();
    }
}
