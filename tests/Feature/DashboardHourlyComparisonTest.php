<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Http\Middleware\EnsureTrustedDevice;
use App\Models\Admin;
use App\Models\Order;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DashboardHourlyComparisonTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_fetch_hourly_order_comparison_for_selected_date(): void
    {
        $admin = Admin::create([
            'name' => 'Admin User',
            'email' => uniqid('admin-', true).'@example.com',
            'password' => 'password',
            'status' => 1,
        ]);

        Order::withoutEvents(function (): void {
            Order::create([
                'invoice_id' => uniqid('INV-HC-', true),
                'customer_name' => 'Customer One',
                'customer_phone' => '01700000101',
                'customer_address' => 'Dhaka',
                'utm_source' => 'direct',
                'status' => OrderStatus::Processing->value,
                'created_at' => '2026-03-10 09:15:00',
                'updated_at' => '2026-03-10 09:15:00',
            ]);

            Order::create([
                'invoice_id' => uniqid('INV-HC-', true),
                'customer_name' => 'Customer Two',
                'customer_phone' => '01700000102',
                'customer_address' => 'Dhaka',
                'utm_source' => 'direct',
                'status' => OrderStatus::Confirmed->value,
                'confirmed_at' => '2026-03-10 10:35:00',
                'created_at' => '2026-03-10 08:05:00',
                'updated_at' => '2026-03-10 10:35:00',
            ]);
        });

        $response = $this->withoutMiddleware(EnsureTrustedDevice::class)
            ->actingAs($admin, 'admin')
            ->get(route('admin.dashboard.hourly_order_comparison', ['date' => '2026-03-10']));

        $response->assertOk()
            ->assertJsonPath('date', '2026-03-10')
            ->assertJsonCount(24, 'labels')
            ->assertJsonCount(24, 'total_orders')
            ->assertJsonCount(24, 'confirmed_orders');

        $payload = $response->json();

        $this->assertSame(1, $payload['total_orders'][8]);
        $this->assertSame(1, $payload['total_orders'][9]);
        $this->assertSame(1, $payload['confirmed_orders'][10]);
        $this->assertSame(0, $payload['confirmed_orders'][9]);
    }
}
