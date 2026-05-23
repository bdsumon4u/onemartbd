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
        $query = Order::query()
            ->select('id', 'status', 'call_campaign_id', 'ai_confirmation_status')
            ->whereIn('status', [OrderStatus::Processing->value, OrderStatus::Confirmed->value])
            ->whereNotNull('call_campaign_id')
            ->orderBy('id');

        $total = $query->count();

        if ($total === 0) {
            $this->info('No processing orders found for call response sync.');

            return self::SUCCESS;
        }

        $this->info(sprintf('Syncing %d call campaigns...', $total));
        $bar = $this->output->createProgressBar($total);

        $updated = 0;

        $query->chunk(100, function ($orders) use (&$updated, $bar) {
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
        });

        $bar->finish();
        $this->newLine();
        $this->info(sprintf('Updated %d order(s).', $updated));

        return self::SUCCESS;
    }
}
