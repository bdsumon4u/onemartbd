<?php

namespace Tests\Feature\API;

use App\Models\AbandonedCart;
use App\Models\Product;
use App\Models\WebSettings;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AbandonedCartForwardingTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        if (! Schema::hasTable('abandoned_carts') || ! Schema::hasTable('products')) {
            $this->markTestSkipped('Required tables are not present for this test database.');
        }
    }

    public function test_master_creates_forwarded_abandoned_cart_from_slave_payload(): void
    {
        $product = Product::factory()->create([
            'name' => 'Fwd Abandoned Item',
            'sku' => 'FWD-AB-'.uniqid(),
            'price' => 1000,
            'status' => 1,
        ]);

        $response = $this->postJson('/api/slave-abandoned-carts', [
            'slave_cart_id' => 501,
            'slave_domain' => 'slave.test',
            'customer_name' => 'Jane',
            'customer_phone' => '01711111111',
            'customer_address' => 'Dhaka',
            'shipping_cost' => 60,
            'subtotal' => 500,
            'total' => 560,
            'discount' => 0,
            'note' => null,
            'status' => 0,
            'items' => [
                [
                    'product_name' => 'Fwd Abandoned Item',
                    'quantity' => 2,
                    'unit_price' => 250,
                    'attributes' => null,
                    'attribute_ids' => null,
                ],
            ],
            'ip_address' => '10.0.0.1',
            'source' => 'direct',
            'utm_source' => 'facebook',
        ]);

        $response->assertCreated();
        $response->assertJsonStructure(['master_abandoned_cart_id', 'status']);

        $this->assertDatabaseHas('abandoned_carts', [
            'slave_id' => 501,
            'slave_domain' => 'slave.test',
            'customer_name' => 'Jane',
            'ip_address' => '10.0.0.1',
            'utm_source' => 'facebook',
        ]);

        $cart = AbandonedCart::query()
            ->where('slave_domain', 'slave.test')
            ->where('slave_id', 501)
            ->first();

        $this->assertNotNull($cart);
        $items = json_decode((string) $cart->abandoned_item, true);
        $this->assertIsArray($items);
        $this->assertSame($product->id, $items[0]['product_id'] ?? null);
    }

    public function test_master_deletes_forwarded_abandoned_cart_when_slave_requests_deletion(): void
    {
        Product::factory()->create([
            'name' => 'Fwd Abandoned Delete',
            'sku' => 'FWD-AB-DEL-'.uniqid(),
            'price' => 500,
            'status' => 1,
        ]);

        $this->postJson('/api/slave-abandoned-carts', [
            'slave_cart_id' => 777,
            'slave_domain' => 'slave-delete.test',
            'customer_name' => 'Bob',
            'customer_phone' => '01722222222',
            'customer_address' => null,
            'shipping_cost' => 0,
            'subtotal' => 500,
            'total' => 500,
            'discount' => 0,
            'status' => 0,
            'items' => [
                [
                    'product_name' => 'Fwd Abandoned Delete',
                    'quantity' => 1,
                    'unit_price' => 500,
                ],
            ],
        ])->assertCreated();

        $this->postJson('/api/slave-abandoned-carts', [
            'slave_cart_id' => 777,
            'slave_domain' => 'slave-delete.test',
            'deleted' => true,
        ])->assertOk()->assertJson(['deleted' => true]);

        $this->assertDatabaseMissing('abandoned_carts', [
            'slave_id' => 777,
            'slave_domain' => 'slave-delete.test',
        ]);
    }

    public function test_non_master_site_rejects_forwarded_abandoned_cart(): void
    {
        if (! Schema::hasTable('web_settings')) {
            $this->markTestSkipped('web_settings table is not present.');
        }

        WebSettings::query()->updateOrInsert(
            ['id' => 1],
            ['master_domain' => 'https://master.example.test']
        );

        $response = $this->postJson('/api/slave-abandoned-carts', [
            'slave_cart_id' => 1,
            'slave_domain' => 'slave.test',
            'deleted' => true,
        ]);

        $response->assertStatus(400);
    }
}
