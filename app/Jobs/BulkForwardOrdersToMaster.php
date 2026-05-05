<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\OrderForwardingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class BulkForwardOrdersToMaster implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected array $orderIds,
    ) {}

    public function handle(OrderForwardingService $forwardingService): void
    {
        try {
            $orders = Order::whereIn('id', $this->orderIds)
                ->whereNull('master_id')
                ->get();

            $successCount = 0;
            $failureCount = 0;

            foreach ($orders as $order) {
                try {
                    $forwardingService->forwardIfConfigured($order, force: true);
                    if ($order->forwarding_status === 'success') {
                        $successCount++;
                    } else {
                        $failureCount++;
                    }
                } catch (\Exception $e) {
                    $failureCount++;
                    Log::warning("Order forwarding failed for order {$order->id}: {$e->getMessage()}");
                }
            }

            Log::info("Bulk order forwarding completed. Success: {$successCount}, Failures: {$failureCount}");
        } catch (\Exception $e) {
            Log::error("Bulk order forwarding job failed: {$e->getMessage()}", [
                'order_ids' => $this->orderIds,
                'exception' => $e,
            ]);

            throw $e;
        }
    }
}
