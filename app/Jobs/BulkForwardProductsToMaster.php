<?php

namespace App\Jobs;

use App\Models\Product;
use App\Services\ProductForwardingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class BulkForwardProductsToMaster implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected array $productIds,
    ) {}

    public function handle(ProductForwardingService $forwardingService): void
    {
        try {
            $products = Product::whereIn('id', $this->productIds)
                ->whereNull('master_id')
                ->get();

            $successCount = 0;
            $failureCount = 0;

            foreach ($products as $product) {
                try {
                    $forwardingService->forwardIfConfigured($product, force: true);
                    if ($product->forwarding_status === 'success') {
                        $successCount++;
                    } else {
                        $failureCount++;
                    }
                } catch (\Exception $e) {
                    $failureCount++;
                    Log::warning("Product forwarding failed for product {$product->id}: {$e->getMessage()}");
                }
            }

            Log::info("Bulk product forwarding completed. Success: {$successCount}, Failures: {$failureCount}");
        } catch (\Exception $e) {
            Log::error("Bulk product forwarding job failed: {$e->getMessage()}", [
                'product_ids' => $this->productIds,
                'exception' => $e,
            ]);

            throw $e;
        }
    }
}
