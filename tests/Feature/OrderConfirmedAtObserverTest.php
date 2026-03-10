<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Date;
use Tests\TestCase;

class OrderConfirmedAtObserverTest extends TestCase
{
    use DatabaseTransactions;

    public function test_sets_confirmed_at_when_status_changes_to_confirmed(): void
    {
        Date::setTestNow('2026-03-10 11:45:00');

        $order = Order::withoutEvents(function (): Order {
            return Order::create([
                'invoice_id' => uniqid('INV-TEST-', true),
                'customer_name' => 'Test Customer',
                'customer_phone' => '01700000001',
                'customer_address' => 'Dhaka',
                'utm_source' => 'direct',
                'status' => OrderStatus::Processing->value,
            ]);
        });

        $order->update([
            'status' => OrderStatus::Confirmed->value,
        ]);

        $this->assertNotNull($order->fresh()->confirmed_at);
        $this->assertSame('2026-03-10 11:45:00', $order->fresh()->confirmed_at?->format('Y-m-d H:i:s'));

        Date::setTestNow();
    }

    public function test_confirmed_at_remains_null_when_status_changes_to_non_confirmed_value(): void
    {
        $order = Order::withoutEvents(function (): Order {
            return Order::create([
                'invoice_id' => uniqid('INV-TEST-', true),
                'customer_name' => 'Test Customer',
                'customer_phone' => '01700000002',
                'customer_address' => 'Dhaka',
                'utm_source' => 'direct',
                'status' => OrderStatus::Hold->value,
            ]);
        });

        $order->update([
            'status' => OrderStatus::Processing->value,
        ]);

        $this->assertNull($order->fresh()->confirmed_at);
    }
}
