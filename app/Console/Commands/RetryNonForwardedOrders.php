<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Product;
use App\Services\OrderForwardingService;
use App\Services\ProductForwardingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RetryNonForwardedOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forwarding:retry-non-forwarded {--type=orders : Type to retry (orders, products, or both)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retry forwarding for orders and products that failed or were not yet forwarded to master';

    public function __construct(
        protected OrderForwardingService $orderForwardingService,
        protected ProductForwardingService $productForwardingService,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $type = $this->option('type');

        try {
            if (in_array($type, ['orders', 'both'])) {
                $this->retryOrders();
            }

            if (in_array($type, ['products', 'both'])) {
                $this->retryProducts();
            }

            $this->info('Forwarding retry completed successfully.');

            return 0;
        } catch (\Exception $e) {
            Log::error('Forwarding retry command failed', [
                'error' => $e->getMessage(),
                'type' => $type,
            ]);

            $this->error("Retry failed: {$e->getMessage()}");

            return 1;
        }
    }

    protected function retryOrders(): void
    {
        // Find orders that are not yet forwarded or failed forwarding
        $orders = Order::query()
            ->whereNull('master_id')
            ->where(function ($query) {
                $query->whereNull('forwarding_status')
                    ->orWhere('forwarding_status', 'failed')
                    ->orWhere('forwarding_status', 'pending');
            })
            ->limit(100) // Process in batches to avoid long-running command
            ->get();

        if ($orders->isEmpty()) {
            $this->info('No non-forwarded orders found.');

            return;
        }

        $this->info("Retrying {$orders->count()} orders...");
        $bar = $this->output->createProgressBar($orders->count());

        $successCount = 0;
        $failureCount = 0;

        foreach ($orders as $order) {
            try {
                $this->orderForwardingService->forwardIfConfigured($order, force: true);
                if ($order->forwarding_status === 'success') {
                    $successCount++;
                } else {
                    $failureCount++;
                }
            } catch (\Exception $e) {
                $failureCount++;
                Log::warning("Order retry failed for order {$order->id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Orders: {$successCount} succeeded, {$failureCount} failed.");
    }

    protected function retryProducts(): void
    {
        // Find products that are not yet forwarded or failed forwarding
        $products = Product::query()
            ->whereNull('master_id')
            ->where(function ($query) {
                $query->whereNull('forwarding_status')
                    ->orWhere('forwarding_status', 'failed')
                    ->orWhere('forwarding_status', 'pending');
            })
            ->limit(100) // Process in batches to avoid long-running command
            ->get();

        if ($products->isEmpty()) {
            $this->info('No non-forwarded products found.');

            return;
        }

        $this->info("Retrying {$products->count()} products...");
        $bar = $this->output->createProgressBar($products->count());

        $successCount = 0;
        $failureCount = 0;

        foreach ($products as $product) {
            try {
                $this->productForwardingService->forwardIfConfigured($product, force: true);
                if ($product->forwarding_status === 'success') {
                    $successCount++;
                } else {
                    $failureCount++;
                }
            } catch (\Exception $e) {
                $failureCount++;
                Log::warning("Product retry failed for product {$product->id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Products: {$successCount} succeeded, {$failureCount} failed.");
    }
}
