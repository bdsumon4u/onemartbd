<?php

namespace App\Services;

use App\Models\Courier;
use App\Models\CourierCity;
use App\Models\CourierZone;
use App\Models\Order;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class OrderCourierService
{
    public function applyCourierChargeCost(Order $order, ?int $courierId, ?int $shippingArea): void
    {
        if (! $courierId || ! $shippingArea) {
            return;
        }

        if (! in_array($courierId, [1, 2, 3], true)) {
            return;
        }

        $courier = Courier::query()->select('id', 'courier_charge_isd', 'courier_charge_osd')->find($courierId);
        if (! $courier) {
            return;
        }

        $charge = match ($shippingArea) {
            1 => (float) ($courier->courier_charge_isd ?? 0),
            2 => (float) ($courier->courier_charge_osd ?? 0),
            default => null,
        };

        if ($charge === null) {
            return;
        }

        $order->update([
            'courier_charge_cost' => $charge > 0 ? $charge : 0,
        ]);
    }

    /**
     * @return array{0: mixed, 1: mixed}
     */
    public function cityAndZoneOptionsForOrder(Order $order): array
    {
        $courierId = (int) ($order->courier_id ?? 0);

        return match ($courierId) {
            1 => $this->pathaoCityAndZoneOptions((int) ($order->courier_city_id ?? 0)),
            2 => $this->redxCityAndZoneOptions(),
            4 => $this->carrybeeCityAndZoneOptions((int) ($order->courier_city_id ?? 0)),
            default => [
                CourierCity::query()
                    ->where([['status', 1], ['courier_id', $courierId]])
                    ->pluck('city_name', 'id'),
                CourierZone::query()
                    ->where([['status', 1], ['courier_id', $courierId]])
                    ->pluck('zone_name', 'id'),
            ],
        };
    }

    /**
     * Send an order to the appropriate courier based on courier_id.
     *
     * @return array{status: 'success'|'error', message: string}
     */
    public function sendOrderToCourier(Order $order): array
    {
        $courierId = (int) ($order->courier_id ?? 0);

        return match ($courierId) {
            1 => $this->sendToPathaoSingle($order),
            2 => $this->sendToRedxSingle($order),
            3 => $this->sendToSteadfastSingle($order),
            default => ['status' => 'error', 'message' => 'Unsupported courier for auto-send'],
        };
    }

    /**
     * @return array{status: 'success'|'error'|'warning', message: string, updated: int}
     */
    public function syncSteadfastStatuses(): array
    {
        $credential = DB::table('stead_fast_apis')
            ->select('is_active', 'api_key', 'secret_key')
            ->where('id', 1)
            ->first();

        if (! $credential) {
            return ['status' => 'error', 'message' => 'SteadFast API credentials not found', 'updated' => 0];
        }

        if ((int) ($credential->is_active ?? 0) !== 1) {
            return ['status' => 'error', 'message' => 'SteadFast Courier API is not active', 'updated' => 0];
        }

        $orders = Order::query()
            ->where('status', 5)
            ->whereNotNull('stead_fast_consignment_id')
            ->get();

        $headers = [
            'Api-Key: '.$credential->api_key,
            'Secret-Key: '.$credential->secret_key,
            'Content-Type: application/json',
        ];

        $updated = 0;

        foreach ($orders as $order) {
            $response = $this->curlJson(
                'https://portal.steadfast.com.bd/api/v1/status_by_trackingcode/'.$order->stead_fast_consignment_id,
                $headers,
                'GET',
            );

            $status = (int) ($response['status'] ?? 0);

            if ($status !== 200) {
                $this->appendLog('stead_fast_sync_log.txt', $response);

                continue;
            }

            $deliveryStatus = (string) ($response['delivery_status'] ?? '');

            if ($deliveryStatus === 'delivered') {
                $order->update(['status' => 1]);
                $updated++;
            } elseif ($deliveryStatus === 'cancelled') {
                $order->update(['status' => 7]);
                $updated++;
            }
        }

        return ['status' => 'success', 'message' => 'SteadFast Status Updated successfully', 'updated' => $updated];
    }

    /**
     * @param  list<int>  $orderIds
     * @return array{status: 'success'|'error'|'warning', message: string}
     */
    public function sendToPathao(array $orderIds): array
    {
        $credential = DB::table('pathao_apis')
            ->select('is_active', 'access_token', 'store_id')
            ->where('id', 1)
            ->first();

        if (! $credential || (int) ($credential->is_active ?? 0) !== 1) {
            return ['status' => 'error', 'message' => 'Pathao Courier API is not active'];
        }

        $accessToken = (string) ($credential->access_token ?? '');
        if ($accessToken === '') {
            return ['status' => 'error', 'message' => 'Pathao Courier API token missing'];
        }

        $storeId = (int) ($credential->store_id ?? 0);

        $ordersPayload = [];

        foreach ($orderIds as $orderId) {
            $order = Order::with('get_products.get_product')
                ->select('id', 'invoice_id', 'customer_name', 'customer_phone', 'customer_address', 'courier_city_id', 'courier_zone_id', 'total', 'due')
                ->find($orderId);

            if (! $order) {
                continue;
            }

            $ordersPayload[$order->id] = [
                'store_id' => $storeId,
                'merchant_order_id' => $order->invoice_id ?? null,
                'recipient_name' => $order->customer_name ?? null,
                'recipient_phone' => $order->customer_phone ?? null,
                'recipient_address' => $order->customer_address ?? null,
                'delivery_area_id' => $order->courier_city_id ?? null,
                'delivery_zone_id' => $order->courier_zone_id ?? null,
                'item_description' => $this->itemDescription($order),
                'item_quantity' => $order->get_products->count() ?? 1,
                'cash_on_delivery' => $order->due ?? 0,
                'item_weight' => 500,
            ];
        }

        if ($ordersPayload === []) {
            return ['status' => 'warning', 'message' => 'No valid orders found for Pathao'];
        }

        $payload = json_encode(['orders' => $ordersPayload]);
        if (! is_string($payload)) {
            return ['status' => 'error', 'message' => 'Failed to build Pathao payload'];
        }

        $headers = [
            'accept: application/json',
            'content-type: application/json',
            'authorization: Bearer '.$accessToken,
        ];

        $data = $this->curlJson(
            'https://api-hermes.pathao.com/aladdin/api/v1/orders/bulk',
            $headers,
            'POST',
            $payload,
        );

        if ((int) ($data['code'] ?? 0) !== 202 && (int) ($data['code'] ?? 0) === 422) {
            foreach (($data['errors'] ?? []) as $key => $messages) {
                Order::find($key)->update([
                    'pathao_entry_error' => json_encode($messages),
                ]);
                $this->appendLog('pathao_entry_log.txt', ['order_id' => $key, 'errors' => $messages]);
            }
        } else {
            $this->appendLog('pathao_entry_log.txt', $data);
        }

        return ['status' => 'success', 'message' => 'Selected orders send to pathao courier'];
    }

    /**
     * @param  list<int>  $orderIds
     * @return array{status: 'success'|'error'|'warning', message: string}
     */
    public function sendToCarrybee(array $orderIds): array
    {
        $credential = DB::table('carry_bee_apis')
            ->select('is_active', 'access_token', 'store_id')
            ->where('id', 1)
            ->first();

        if (! $credential || (int) ($credential->is_active ?? 0) !== 1) {
            return ['status' => 'error', 'message' => 'Carrybee Courier API is not active'];
        }

        $accessToken = (string) ($credential->access_token ?? '');
        if ($accessToken === '') {
            return ['status' => 'error', 'message' => 'Carrybee Courier API token missing'];
        }

        $storeId = (int) ($credential->store_id ?? 0);

        $anyAttempted = false;

        foreach ($orderIds as $orderId) {
            $order = Order::with('get_products.get_product')
                ->select('id', 'invoice_id', 'customer_name', 'customer_phone', 'customer_address', 'courier_city_id', 'courier_zone_id', 'total', 'due')
                ->find($orderId);

            if (! $order) {
                continue;
            }

            $anyAttempted = true;

            $payload = [
                'store_id' => $storeId,
                'merchant_order_id' => $order->invoice_id ?? null,
                'recipient_name' => $order->customer_name ?? null,
                'recipient_phone' => $order->customer_phone ?? null,
                'recipient_address' => $order->customer_address ?? null,
                'delivery_area_id' => $order->courier_city_id ?? null,
                'delivery_zone_id' => $order->courier_zone_id ?? null,
                'item_description' => $this->itemDescription($order),
                'item_quantity' => $order->get_products->count() ?? 1,
                'cash_on_delivery' => $order->due ?? 0,
                'item_weight' => 500,
            ];

            $json = json_encode($payload);
            if (! is_string($json)) {
                $this->appendLog('carrybee_entry_log.txt', ['error' => 'Failed to encode payload', 'order_id' => $orderId]);

                continue;
            }

            $headers = [
                'accept: application/json',
                'content-type: application/json',
                'authorization: Bearer '.$accessToken,
            ];

            $data = $this->curlJson('https://developers.carrybee.com/api/orders', $headers, 'POST', $json);

            if (($data['success'] ?? null) === true) {
                $order->update(['carrybee_consignment_id' => $data['data']['tracking_id'] ?? null]);
                $this->appendLog('carrybee_entry_log.txt', ['success' => true, 'order_id' => $orderId]);
            } else {
                $this->appendLog('carrybee_entry_log.txt', ['error' => $data, 'order_id' => $orderId]);
            }
        }

        if (! $anyAttempted) {
            return ['status' => 'warning', 'message' => 'No valid orders found for Carrybee'];
        }

        return ['status' => 'success', 'message' => 'Selected orders send to Carrybee courier'];
    }

    /**
     * Send a single order to Pathao courier.
     *
     * @return array{status: 'success'|'error', message: string}
     */
    public function sendToPathaoSingle(Order $order): array
    {
        $credential = DB::table('pathao_apis')
            ->select('is_active', 'access_token', 'store_id')
            ->where('id', 1)
            ->first();

        if (! $credential || (int) ($credential->is_active ?? 0) !== 1) {
            return ['status' => 'error', 'message' => 'Pathao Courier API is not active'];
        }

        $accessToken = (string) ($credential->access_token ?? '');
        if ($accessToken === '') {
            return ['status' => 'error', 'message' => 'Pathao Courier API token missing'];
        }

        $storeId = (int) ($credential->store_id ?? 0);

        $order = $order->load('get_products.get_product');

        $payload = json_encode([
            'orders' => [
                [
                    'store_id' => $storeId,
                    'merchant_order_id' => $order->invoice_id ?? null,
                    'recipient_name' => $order->customer_name ?? null,
                    'recipient_phone' => $order->customer_phone ?? null,
                    'recipient_address' => $order->customer_address ?? null,
                    'delivery_area_id' => $order->courier_city_id ?? null,
                    'delivery_zone_id' => $order->courier_zone_id ?? null,
                    'item_description' => $this->itemDescription($order),
                    'item_quantity' => $order->get_products->count() ?? 1,
                    'cash_on_delivery' => $order->due ?? 0,
                    'item_weight' => 500,
                ],
            ],
        ]);

        if (! is_string($payload)) {
            return ['status' => 'error', 'message' => 'Failed to build Pathao payload'];
        }

        $headers = [
            'accept: application/json',
            'content-type: application/json',
            'authorization: Bearer '.$accessToken,
        ];

        $data = $this->curlJson(
            'https://api-hermes.pathao.com/aladdin/api/v1/orders/bulk',
            $headers,
            'POST',
            $payload,
        );

        if ((int) ($data['code'] ?? 0) !== 202) {
            $this->appendLog('pathao_entry_log.txt', $data);

            return ['status' => 'error', 'message' => 'Failed to send order to Pathao'];
        }

        $consignmentId = $data['data'][0]['consignment_id'] ?? null;
        $order->update(['pathao_consignment_id' => $consignmentId]);

        return ['status' => 'success', 'message' => 'Order sent to Pathao courier'];
    }

    /**
     * Send a single order to RedX courier.
     *
     * @return array{status: 'success'|'error', message: string}
     */
    public function sendToRedxSingle(Order $order): array
    {
        $credential = DB::table('redx_apis')
            ->select('is_active', 'access_token')
            ->where('id', 1)
            ->first();

        if (! $credential || (int) ($credential->is_active ?? 0) !== 1) {
            return ['status' => 'error', 'message' => 'RedX Courier API is not active'];
        }

        $accessToken = (string) ($credential->access_token ?? '');
        if ($accessToken === '') {
            return ['status' => 'error', 'message' => 'RedX Courier API token missing'];
        }

        $headers = [
            'API-ACCESS-TOKEN: Bearer '.$accessToken,
        ];

        // Fetch delivery area
        $areasResponse = $this->curlJson(
            'https://openapi.redx.com.bd/v1.0.0-beta/areas',
            $headers,
            'GET',
        );

        $deliveryArea = null;
        foreach (($areasResponse['areas'] ?? []) as $area) {
            if ((int) ($area['id'] ?? 0) === (int) ($order->courier_city_id ?? 0)) {
                $deliveryArea = $area['name'] ?? null;
                break;
            }
        }

        if (! is_string($deliveryArea) || $deliveryArea === '') {
            return ['status' => 'error', 'message' => 'RedX delivery area not found'];
        }

        $payload = json_encode([
            'customer_name' => $order->customer_name ?? null,
            'customer_phone' => $order->customer_phone ?? null,
            'delivery_area' => $deliveryArea,
            'delivery_area_id' => $order->courier_city_id ?? null,
            'customer_address' => $order->customer_address ?? null,
            'merchant_invoice_id' => $order->invoice_id ?? null,
            'cash_collection_amount' => $order->due ?? 0,
            'parcel_weight' => 500,
            'instruction' => '',
            'value' => $order->due ?? 0,
        ]);

        if (! is_string($payload)) {
            return ['status' => 'error', 'message' => 'Failed to build RedX payload'];
        }

        $headers[] = 'Content-Type: application/json';

        $data = $this->curlJson(
            'https://openapi.redx.com.bd/v1.0.0-beta/parcel',
            $headers,
            'POST',
            $payload,
        );

        $trackingId = $data['tracking_id'] ?? null;
        if (! is_string($trackingId) || $trackingId === '') {
            $this->appendLog('redx_entry_log.txt', $data);

            return ['status' => 'error', 'message' => 'Failed to send order to RedX'];
        }

        $order->update(['redx_tracking_id' => $trackingId]);

        return ['status' => 'success', 'message' => 'Order sent to RedX courier'];
    }

    /**
     * Send a single order to Steadfast courier.
     *
     * @return array{status: 'success'|'error', message: string}
     */
    public function sendToSteadfastSingle(Order $order): array
    {
        $credential = DB::table('stead_fast_apis')
            ->select('is_active', 'api_key', 'secret_key')
            ->where('id', 1)
            ->first();

        if (! $credential || (int) ($credential->is_active ?? 0) !== 1) {
            return ['status' => 'error', 'message' => 'Steadfast Courier API is not active'];
        }

        $apiKey = (string) ($credential->api_key ?? '');
        $secretKey = (string) ($credential->secret_key ?? '');

        if ($apiKey === '' || $secretKey === '') {
            return ['status' => 'error', 'message' => 'Steadfast Courier API credentials missing'];
        }

        $payload = json_encode([
            'invoice' => $order->invoice_id ?? null,
            'recipient_name' => $order->customer_name ?? null,
            'recipient_address' => $order->customer_address ?? null,
            'recipient_phone' => $order->customer_phone ?? null,
            'cod_amount' => $order->total ?? 0,
            'note' => '',
        ]);

        if (! is_string($payload)) {
            return ['status' => 'error', 'message' => 'Failed to build Steadfast payload'];
        }

        $headers = [
            'Api-Key: '.$apiKey,
            'Secret-Key: '.$secretKey,
            'Content-Type: application/json',
        ];

        $data = $this->curlJson(
            'https://portal.steadfast.com.bd/api/v1/create_order',
            $headers,
            'POST',
            $payload,
        );

        if ((int) ($data['status'] ?? 0) !== 200) {
            $this->appendLog('stead_fast_entry_log.txt', $data);

            return ['status' => 'error', 'message' => 'Failed to send order to Steadfast'];
        }

        $trackingCode = $data['consignment']['tracking_code'] ?? null;
        $order->update(['stead_fast_consignment_id' => $trackingCode]);

        return ['status' => 'success', 'message' => 'Order sent to Steadfast courier'];
    }

    private function itemDescription(Order $order): string
    {
        $lines = [];

        foreach ($order->get_products ?? [] as $orderProduct) {
            $name = $orderProduct->get_product->name ?? null;

            if (is_string($name) && $name !== '') {
                $lines[] = $name;
            }
        }

        return implode("\n", $lines);
    }

    /**
     * @param  list<string>  $headers
     * @return array<string, mixed>
     */
    private function curlJson(string $url, array $headers, string $method = 'GET', ?string $body = null): array
    {
        $curl = curl_init();

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
        ];

        if ($body !== null) {
            $options[CURLOPT_POSTFIELDS] = $body;
        }

        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        curl_close($curl);

        if (! is_string($response) || $response === '') {
            return [];
        }

        $decoded = json_decode($response, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function appendLog(string $fileName, mixed $data): void
    {
        $date = Date::now()."\n";
        $path = base_path('storage/logs/'.$fileName);

        $fp = fopen($path, 'a');
        if ($fp === false) {
            return;
        }

        fwrite($fp, $date.json_encode($data, JSON_PRETTY_PRINT)."\n\n");
        fclose($fp);
    }

    /**
     * @return array{0: array<int, string>, 1: array<int, string>}
     */
    public function pathaoCityAndZoneOptions(int $cityId): array
    {
        $credential = DB::table('pathao_apis')
            ->select('is_active', 'access_token')
            ->where('id', 1)
            ->first();

        if (! $credential || (int) ($credential->is_active ?? 0) !== 1 || ! $credential->access_token) {
            return [[], []];
        }

        $headers = [
            'accept: application/json',
            'content-type: application/json',
            'Authorization: Bearer '.$credential->access_token,
        ];

        $citiesResponse = $this->curlJson(
            'https://api-hermes.pathao.com/aladdin/api/v1/countries/1/city-list',
            $headers,
            'GET',
        );

        $cities = [];
        foreach (($citiesResponse['data']['data'] ?? []) as $item) {
            if (isset($item['city_id'], $item['city_name'])) {
                $cities[(int) $item['city_id']] = (string) $item['city_name'];
            }
        }

        $zones = [];
        if ($cityId > 0) {
            $zonesResponse = $this->curlJson(
                'https://api-hermes.pathao.com/aladdin/api/v1/cities/'.$cityId.'/zone-list',
                $headers,
                'GET',
            );

            foreach (($zonesResponse['data']['data'] ?? []) as $item) {
                if (isset($item['zone_id'], $item['zone_name'])) {
                    $zones[(int) $item['zone_id']] = (string) $item['zone_name'];
                }
            }
        }

        return [$cities, $zones];
    }

    /**
     * @return array{0: array<int, string>, 1: array<int, string>}
     */
    public function carrybeeCityAndZoneOptions(int $cityId): array
    {
        $credential = DB::table('carry_bee_apis')
            ->select('is_active', 'access_token')
            ->where('id', 1)
            ->first();

        if (! $credential || (int) ($credential->is_active ?? 0) !== 1 || ! $credential->access_token) {
            return [[], []];
        }

        $headers = [
            'accept: application/json',
            'content-type: application/json',
            'Authorization: Bearer '.$credential->access_token,
        ];

        $citiesResponse = $this->curlJson('https://developers.carrybee.com/api/city-list', $headers, 'GET');

        $cities = [];
        foreach (($citiesResponse['data']['data'] ?? []) as $item) {
            if (isset($item['city_id'], $item['city_name'])) {
                $cities[(int) $item['city_id']] = (string) $item['city_name'];
            }
        }

        $zones = [];
        if ($cityId > 0) {
            $zonesResponse = $this->curlJson(
                'https://developers.carrybee.com/api/cities/'.$cityId.'/zones',
                $headers,
                'GET',
            );

            foreach (($zonesResponse['data']['data'] ?? []) as $item) {
                if (isset($item['zone_id'], $item['zone_name'])) {
                    $zones[(int) $item['zone_id']] = (string) $item['zone_name'];
                }
            }
        }

        return [$cities, $zones];
    }

    /**
     * @return array{0: array<int, string>, 1: array<int, string>}
     */
    public function redxCityAndZoneOptions(): array
    {
        $credential = DB::table('redx_apis')
            ->select('is_active', 'access_token')
            ->where('id', 1)
            ->first();

        if (! $credential || (int) ($credential->is_active ?? 0) !== 1 || ! $credential->access_token) {
            return [[], []];
        }

        $headers = [
            'API-ACCESS-TOKEN: Bearer '.$credential->access_token,
        ];

        $areasResponse = $this->curlJson('https://openapi.redx.com.bd/v1.0.0-beta/areas', $headers, 'GET');
        $areas = $areasResponse['areas'] ?? [];

        $cities = [];
        foreach ($areas as $item) {
            if (! isset($item['id'])) {
                continue;
            }

            $cities[(int) $item['id']] = trim(
                (string) ($item['division_name'] ?? '').' > '.(string) ($item['district_name'] ?? '').' > '.(string) ($item['name'] ?? '')
            );
        }

        return [$cities, []];
    }
}
