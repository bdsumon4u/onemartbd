<?php

namespace App\Http\Services;

use Log;
use App\WebSettings;

class WhatsappServices
{
    public $phone_id, $token;

    public function __construct()
    {
        $api_settings = WebSettings::find(1);
        $this->phone_id = $api_settings->wp_phone_number_id;
        $this->token = $api_settings->wp_access_token;
    }

    public function sendOrderWhatsapp($order, $template_name, $status)
    {
        // dd(7777);
        if (!$order || !$order->customer_phone || !$template_name) {
            Log::error('❌ WhatsApp: Missing required data', compact('order', 'template_name'));
            return;
        }

        $to = $this->cleanAndFormatPhoneNumber($order->customer_phone);
        $parameters = [];

        if ($status == 9 || $status == 2 || $status == 4 || $status == 7 || $status == 1) {
            $parameters = [
                ["type" => "text", "text" => $order->invoice_id],
            ];
        } elseif ($status == 13) {
            $products = $order->get_products->map(function ($item) {
                return $item->qty . ' x ' . ($item->get_product->name ?? '');
            })->implode(', ');
            $parameters = [
                ["type" => "text", "text" => $order->invoice_id],
                ["type" => "text", "text" => $products],
                ["type" => "text", "text" => $order->total],
            ];
        } elseif ($status == 6) {
            if ($order->pathao_consignment_id) {
                $link = "https://merchant.pathao.com/tracking?consignment_id={$order->pathao_consignment_id}&phone={$order->customer_phone}";
            } elseif ($order->redx_tracking_id) {
                $link = "https://redx.com.bd/track-parcel/?trackingId={$order->redx_tracking_id}";
            } elseif ($order->carrybee_consignment_id) {
                $link = "https://merchant.carrybee.com/tracking?consignment_id={$order->carrybee_consignment_id}";
            } else {
                $link = 'NC';
            }

            $parameters = [
                ["type" => "text", "text" => $order->invoice_id],
                ["type" => "text", "text" => $link],
            ];
        }
        $data = [
            "messaging_product" => "whatsapp",
            "to" => $to,
            "type" => "template",
            "template" => [
                "name" => $template_name,
                "language" => [
                    "code" => "bn",
                ],
                "components" => [
                    [
                        "type" => "body",
                        "parameters" => $parameters,
                    ],
                ],
            ],
        ];

        // dd($data);
        $this->apiCall($data);
    }

    private function cleanAndFormatPhoneNumber($phone)
    {
        $phone = preg_replace('/[^\d]/', '', $phone);
        $phone = trim($phone);

        if (\Illuminate\Support\Str::startsWith($phone, '880')) {
            return $phone;
        }

        return '880' . ltrim($phone, '0');
    }

    private function apiCall($data)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://graph.facebook.com/v24.0/" . $this->phone_id . "/messages",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data, JSON_UNESCAPED_UNICODE),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Authorization: Bearer " . $this->token
            ],
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            \Log::error('❌ cURL Error: ' . $error);
            return false;
        }

        $decoded = json_decode($response, true);
        // dd($decoded);
        if (isset($decoded['messages'])) {
            \Log::info('✅ WhatsApp message sent successfully', $decoded);
        } else {
            \Log::error('❌ Failed to send WhatsApp message', $decoded ?? []);
        }

        return $decoded;
    }
}
