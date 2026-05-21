<?php

namespace App\Jobs;

use App\Models\CallAutomationSetting;
use App\Models\Order;
use App\Services\OrderCallAutomationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class StartOrderCallCampaign implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public function __construct(public int $orderId)
    {
        $this->afterCommit();
    }

    public function handle(OrderCallAutomationService $orderCallAutomationService): void
    {
        $settings = CallAutomationSetting::first();

        if (! ($settings?->enabled ?? true)) {
            Log::info('Call automation skipped because it is disabled', [
                'order_id' => $this->orderId,
            ]);

            return;
        }

        $order = Order::query()->find($this->orderId);

        if (! $order) {
            Log::warning('Call automation job could not find order', [
                'order_id' => $this->orderId,
            ]);

            return;
        }

        $orderCallAutomationService->startCampaign($order);
    }

    public function middleware(): array
    {
        return [new WithoutOverlapping($this->orderId)];
    }
}