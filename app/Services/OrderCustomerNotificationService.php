<?php

namespace App\Services;

use App\Models\Order;
use App\Models\SmsSetting;
use App\Models\WebSettings;
use Carbon\Carbon;

class OrderCustomerNotificationService
{
    public function __construct(
        protected WhatsappServices $whatsappServices,
    ) {}

    public function notifyForStatusChange(Order $order, int $status, ?SmsSetting $sms = null): void
    {
        $sms ??= SmsSetting::where('status', $status)->first();

        if ($sms && (int) $sms->is_active === 1) {
            $this->sendSmsToCustomer($order, $sms);
        }

        $this->notifyWhatsappFromSetting($order, $sms);
    }

    public function notifyWhatsappForStatus(Order $order, int $status, ?SmsSetting $sms = null): void
    {
        $sms ??= SmsSetting::where('status', $status)->first();

        $this->notifyWhatsappFromSetting($order, $sms);
    }

    public function sendOrderConfirmSmsIfEnabled(Order $order): void
    {
        $settings = WebSettings::query()
            ->select('id', 'is_order_confirm_sms', 'order_confirm_sms', 'is_sms_enabled', 'sms_start_time', 'sms_end_time')
            ->find(1);

        if (! $settings || (int) ($settings->is_order_confirm_sms ?? 0) !== 1 || ! $settings->order_confirm_sms) {
            return;
        }

        // Check global SMS settings
        if (! $this->isSmsAllowed($settings)) {
            return;
        }

        $order->loadMissing('get_products.get_product');

        $products = '';
        foreach ($order->get_products as $key => $item) {
            if ($key != 0) {
                $products .= "\n";
            }
            $products .= $item->get_product->name.'.';
        }

        $mgs_body = strtr((string) $settings->order_confirm_sms, [
            '{$invoice_id}' => $order->invoice_id ?? null,
            '{$products}' => $products ?? null,
            '{$total_amount}' => $order->total ?? 0,
        ]);

        $apikey = config('app.sms_api_key');
        $msisdn = ltrim((string) BanglaToEnglishConverter::bn2en($order->customer_phone), '+');

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.sms.net.bd/sendsms',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => ['api_key' => $apikey, 'msg' => $mgs_body, 'to' => $msisdn],
        ]);

        curl_exec($curl);
        curl_close($curl);
    }

    private function isSmsAllowed(?WebSettings $settings = null): bool
    {
        $settings ??= WebSettings::query()
            ->select('id', 'is_sms_enabled', 'sms_start_time', 'sms_end_time')
            ->find(1);

        if (! $settings) {
            return true; // If no settings, allow SMS
        }

        // Check if SMS is globally enabled
        if (! $settings->is_sms_enabled) {
            return false;
        }

        // Check time range if both start and end times are set
        if ($settings->sms_start_time && $settings->sms_end_time) {
            $currentTime = Carbon::now()->format('H:i:s');
            $startTime = $settings->sms_start_time;
            $endTime = $settings->sms_end_time;

            // If start time is less than end time (same day range)
            if ($startTime < $endTime) {
                return $currentTime >= $startTime && $currentTime <= $endTime;
            }

            // If start time is greater than end time (overnight range)
            return $currentTime >= $startTime || $currentTime <= $endTime;
        }

        return true; // Allow SMS if time range is not fully configured
    }

    private function notifyWhatsappFromSetting(Order $order, ?SmsSetting $sms): void
    {
        if (! $this->isSmsAllowed()) {
            return;
        }

        if ($sms && (int) $sms->is_whatsapp === 1 && $sms->template_name != null) {
            $this->whatsappServices->sendOrderWhatsapp($order, $sms->template_name, $sms->status);
        }
    }

    private function sendSmsToCustomer(Order $order, SmsSetting $sms): void
    {
        if (! $this->isSmsAllowed()) {
            return;
        }

        $products = '';
        foreach ($order->get_products as $key => $item) {
            if ($key != 0) {
                $products .= "\n";
            }
            $products .= $item->qty.' x '.$item->get_product->name;
        }

        $mgs_body = strtr($sms->message, [
            '{$invoice_id}' => $order->invoice_id ?? null,
            '{$products}' => $products ?? null,
            '{$total_amount}' => $order->total ?? 0,
        ]);

        $apikey = config('app.sms_api_key');

        $msisdn = ltrim((string) BanglaToEnglishConverter::bn2en($order->customer_phone), '+');

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.sms.net.bd/sendsms',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => ['api_key' => $apikey, 'msg' => $mgs_body, 'to' => $msisdn],
        ]);

        curl_exec($curl);

        curl_close($curl);
    }
}
