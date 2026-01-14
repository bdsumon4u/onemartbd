<?php

namespace App\Services;

use App\Models\Order;

class OrderInvoiceIdGenerator
{
    public function next(): string
    {
        $latestInvoiceId = Order::query()->latest('id')->value('invoice_id');

        $latestNumber = 0;
        if (is_string($latestInvoiceId) && preg_match('/(\d+)$/', $latestInvoiceId, $matches) === 1) {
            $latestNumber = (int) $matches[1];
        }

        return 'INV'.($latestNumber + 1);
    }
}
