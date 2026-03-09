<?php

namespace Tests\Feature;

use App\Models\LandingPage;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_landing_page_with_product(): void
    {
        // Create a test product
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'sku' => 'TEST-001',
            'price' => 1000,
            'status' => 1,
        ]);

        // Create a landing page
        $landingPage = LandingPage::create([
            'product_id' => $product->id,
            'title' => 'Amazing Test Product',
            'subtitle' => 'The best product ever',
            'slug' => 'amazing-test-product',
            'status' => true,
        ]);

        $this->assertDatabaseHas('landing_pages', [
            'title' => 'Amazing Test Product',
            'slug' => 'amazing-test-product',
            'product_id' => $product->id,
        ]);
    }

    public function test_landing_page_displays_correctly(): void
    {
        // Create a test product
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 1000,
            'status' => 1,
        ]);

        // Create a landing page
        $landingPage = LandingPage::create([
            'product_id' => $product->id,
            'title' => 'Amazing Test Product',
            'slug' => 'test-landing-page',
            'status' => true,
        ]);

        // Visit the landing page
        $response = $this->get(route('landing.page', $landingPage->slug));

        $response->assertStatus(200)
            ->assertSee('Amazing Test Product')
            ->assertSee($product->name);
    }

    public function test_landing_page_fallback_images_work(): void
    {
        // Create test product with images
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'status' => 1,
        ]);

        // Create landing page without banner
        $landingPage = LandingPage::create([
            'product_id' => $product->id,
            'title' => 'Test Landing Page',
            'slug' => 'test-landing-page',
            'status' => true,
        ]);

        // Test that display_banner returns fallback
        $this->assertNotNull($landingPage->display_banner);
        $this->assertStringContainsString('no_image.png', $landingPage->display_banner);
    }

    public function test_landing_page_model_relationships(): void
    {
        // Create test product
        $product = Product::factory()->create();

        // Create landing page
        $landingPage = LandingPage::create([
            'product_id' => $product->id,
            'title' => 'Test Landing Page',
            'slug' => 'test-landing-page',
            'status' => true,
        ]);

        // Test relationships
        $this->assertEquals($product->id, $landingPage->product->id);
        $this->assertEquals($product->name, $landingPage->product->name);
    }
}
