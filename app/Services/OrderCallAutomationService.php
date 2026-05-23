<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\CallAutomationSetting;
use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class OrderCallAutomationService
{
    public function __construct(
        protected OrderCourierService $orderCourierService,
    ) {}

    public function startCampaign(Order $order): ?string
    {
        if (! $this->isEnabled()) {
            Log::info('Call automation skipped because it is disabled', [
                'order_id' => $order->id,
            ]);

            return null;
        }

        if (! empty($order->call_campaign_id)) {
            return (string) $order->call_campaign_id;
        }

        $payload = $this->startPayload($order);

        Log::info('Call automation request outgoing', [
            'order_id' => $order->id,
            'url' => $this->callUrl(),
            'payload' => $payload,
        ]);

        try {
            $response = $this->client()->asJson()->post($this->callUrl(), $payload);
        } catch (Throwable $throwable) {
            Log::warning('Call automation request failed', [
                'order_id' => $order->id,
                'error' => $throwable->getMessage(),
                'payload' => $payload,
            ]);

            return null;
        }

        if (! $response->successful()) {
            Log::warning('Call automation API returned an error response', [
                'order_id' => $order->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }

        $campaignId = $this->extractCampaignId($response->json());

        if (! $campaignId) {
            Log::warning('Call automation campaign id was missing from the response', [
                'order_id' => $order->id,
                'body' => $response->json(),
            ]);

            return null;
        }

        $order->update([
            'call_campaign_id' => $campaignId,
            'ai_confirmation_status' => $order->ai_confirmation_status ?: 'pending',
        ]);

        return $campaignId;
    }

    public function syncOrderResponse(Order $order): bool
    {
        if (empty($order->call_campaign_id)) {
            return false;
        }

        try {
            $response = $this->client()->get($this->checkResponseUrl(), [
                'apiKey' => $this->apiKey(),
                'campaignId' => $order->call_campaign_id,
            ]);
        } catch (Throwable $throwable) {
            Log::warning('Call automation check response failed', [
                'order_id' => $order->id,
                'campaign_id' => $order->call_campaign_id,
                'error' => $throwable->getMessage(),
            ]);

            return false;
        }

        if (! $response->successful()) {
            Log::warning('Call automation check response returned an error', [
                'order_id' => $order->id,
                'campaign_id' => $order->call_campaign_id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        }

        $decision = $this->extractDecision($response->json());

        if (! $decision) {
            return false;
        }

        $updates = [
            'ai_confirmation_status' => $decision,
            'ai_confirmation_checked_at' => now(),
        ];

        if ($decision === 'confirmed') {
            $updates['status'] = OrderStatus::Confirmed->value;
            $pathaoUpdates = $this->orderCourierService->getPathaoAutoAssignmentUpdates($order);
            $updates = array_merge($updates, $pathaoUpdates);
        } elseif ($decision === 'rejected') {
            $updates['status'] = OrderStatus::Cancelled->value;
        }

        $order->update($updates);

        return true;
    }

    public function retryCampaign(string $campaignId): array
    {
        try {
            $response = $this->client()->get($this->retryUrl(), [
                'apiKey' => $this->apiKey(),
                'campaignId' => $campaignId,
            ]);

            Log::info('Call retry response', [
                'campaign_id' => $campaignId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (Throwable $throwable) {
            Log::warning('Call automation retry request failed', [
                'campaign_id' => $campaignId,
                'error' => $throwable->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $throwable->getMessage(),
            ];
        }

        if (! $response->successful()) {
            $message = (string) ($response->json('message') ?? $response->json('error') ?? $response->body() ?? 'Unable to retry call');

            Log::warning('Call automation retry API returned an error', [
                'campaign_id' => $campaignId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => $message,
            ];
        }

        return [
            'success' => true,
            'message' => (string) ($response->json('message') ?? 'Call retry sent successfully'),
        ];
    }

    protected function startPayload(Order $order): array
    {
        return [
            'apiKey' => $this->apiKey(),
            'did' => $this->did(),
            'phone' => trim((string) $order->customer_phone),
            'maintext' => $this->mainText($order),
            'text1' => $this->textOne(),
            'text2' => $this->textTwo(),
        ];
    }

    protected function client()
    {
        return Http::acceptJson()->timeout(20);
    }

    protected function apiKey(): string
    {
        $s = CallAutomationSetting::first();

        return (string) ($s->api_key ?? config('services.call_automation.api_key', ''));
    }

    protected function did(): string
    {
        $s = CallAutomationSetting::first();

        return trim((string) ($s->did ?? config('services.call_automation.did', '')));
    }

    protected function mainText(Order $order): string
    {
        $s = CallAutomationSetting::first();

        return $this->renderTemplate((string) ($s->maintext ?? config('services.call_automation.maintext', 'Hello')), $order);
    }

    protected function textOne(): string
    {
        $s = CallAutomationSetting::first();

        return (string) ($s->text1 ?? config('services.call_automation.text1', 'Thanks For Pressing 1'));
    }

    protected function textTwo(): string
    {
        $s = CallAutomationSetting::first();

        return (string) ($s->text2 ?? config('services.call_automation.text2', 'Thanks For Pressing 2'));
    }

    protected function callUrl(): string
    {
        $s = CallAutomationSetting::first();

        return (string) ($s->call_url ?? config('services.call_automation.call_url', ''));
    }

    protected function retryUrl(): string
    {
        $s = CallAutomationSetting::first();

        return (string) ($s->retry_url ?? config('services.call_automation.retry_url', ''));
    }

    protected function checkResponseUrl(): string
    {
        $s = CallAutomationSetting::first();

        return (string) ($s->check_response_url ?? config('services.call_automation.check_response_url', ''));
    }

    protected function renderTemplate(string $template, ?Order $order): string
    {
        if (! $order) {
            return $template;
        }

        $order->loadMissing('products.product');

        $products = $order->products;
        $productNames = $products
            ->map(function (OrderProduct $orderProduct): string {
                $productName = (string) ($orderProduct->product?->name ?? '');
                $quantity = (int) ($orderProduct->qty ?? 0);

                if ($productName === '') {
                    return '';
                }

                return $quantity > 1 ? $productName.' x '.$quantity : $productName;
            })
            ->filter()
            ->values();

        $replacements = [
            '{order_id}' => (string) $order->id,
            '{invoice_id}' => (string) $order->invoice_id,
            '{customer_name}' => (string) $order->customer_name,
            '{customer_phone}' => (string) $order->customer_phone,
            '{customer_address}' => (string) $order->customer_address,
            '{product_name}' => (string) ($productNames->first() ?? ''),
            '{product_names}' => $productNames->implode(', '),
            '{amount}' => number_format((float) ($order->total ?? 0), 2, '.', ''),
            '{total}' => number_format((float) ($order->total ?? 0), 2, '.', ''),
            '{paid}' => number_format((float) ($order->paid ?? 0), 2, '.', ''),
            '{due}' => number_format((float) ($order->due ?? 0), 2, '.', ''),
            '{status}' => (string) ($order->status ?? ''),
            '{quantity}' => (string) $products->sum('qty'),
        ];

        return strtr($template, $replacements);
    }

    protected function isEnabled(): bool
    {
        $settings = CallAutomationSetting::first();

        return (bool) ($settings?->enabled ?? true);
    }

    protected function extractCampaignId(mixed $payload): ?string
    {
        if (! is_array($payload)) {
            return null;
        }

        foreach (['campaignId', 'campaign_id', 'data.campaignId', 'data.campaign_id', 'result.campaignId', 'result.campaign_id'] as $path) {
            $value = data_get($payload, $path);

            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }

            if (is_numeric($value)) {
                return (string) $value;
            }
        }

        return null;
    }

    protected function extractDecision(mixed $payload): ?string
    {
        if (! is_array($payload)) {
            return null;
        }

        // direct status code mapping from provider
        $rawStatus = strtolower((string) data_get($payload, 'status', ''));
        if ($rawStatus === 'keyp1') {
            return 'confirmed';
        }

        if ($rawStatus === 'keyp2') {
            return 'rejected';
        }

        if ($rawStatus !== '') {
            return $rawStatus;
        }

        return null;
    }

    protected function containsAny(string $value, array $needles): bool
    {
        foreach ($needles as $needle) {
            if ($needle !== '' && str_contains($value, $needle)) {
                return true;
            }
        }

        return false;
    }
}
