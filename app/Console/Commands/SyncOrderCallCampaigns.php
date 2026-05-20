<?php

namespace App\Console\Commands;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\OrderCallAutomationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncOrderCallCampaigns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:sync-call-confirmations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync call automation responses for processing orders';

    public function __construct(
        protected OrderCallAutomationService $orderCallAutomationService,
    ) {
        parent::__construct();
    }

    /**
     * Execute the command.
     */
    public function handle(): int
    {
        $orders = Order::query()
            ->select('id', 'status', 'call_campaign_id', 'ai_confirmation_status')
            ->where('status', OrderStatus::Processing->value)
            ->whereNotNull('call_campaign_id')
            ->where(function ($query): void {
                $query->whereNull('ai_confirmation_status')
                    ->orWhere('ai_confirmation_status', 'pending');
            })
            ->orderBy('id')
            ->limit(100)
            ->get();

        if ($orders->isEmpty()) {
            $this->info('No processing orders found for call response sync.');

            return self::SUCCESS;
        }

        $this->info(sprintf('Syncing %d call campaigns...', $orders->count()));
        $bar = $this->output->createProgressBar($orders->count());

        $updated = 0;

        foreach ($orders as $order) {
            try {
                if ($this->orderCallAutomationService->syncOrderResponse($order)) {
                    $updated++;
                }
            } catch (\Throwable $throwable) {
                Log::warning('Order call sync failed', [
                    'order_id' => $order->id,
                    'error' => $throwable->getMessage(),
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info(sprintf('Updated %d order(s).', $updated));

        return self::SUCCESS;
    }
}
