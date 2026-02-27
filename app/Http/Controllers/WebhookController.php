<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebhookController extends Controller
{
    private const PATHAO_SECRET = 'f3992ecc-59da-4cbe-a049-a13da2018d51';

    private const CARRYBEE_SIGNATURE = '40489fe0-9386-4fc9-8e92-2b2fcb9d451c';

    public function pathao(Request $request)
    {
        $payload = $this->decodePayload($request);
        if (! $payload) {
            return $this->unsupported();
        }

        if ($payload['event'] === 'webhook_integration') {
            return response()->json([
                'status' => 'accepted',
                'message' => 'Webhook received successfully',
            ], 202)->header('X-Pathao-Merchant-Webhook-Integration-Secret', self::PATHAO_SECRET);
        }

        $settings = DB::table('pathao_apis')->select('id', 'store_id')->first();
        if ($settings && ($payload['store_id'] ?? null) === $settings->store_id) {
            $this->processPathaoEvent($payload);
        }

        return response()->json()->header('X-Pathao-Merchant-Webhook-Integration-Secret', self::PATHAO_SECRET);
    }

    public function redx(Request $request)
    {
        $payload = $this->decodePayload($request);
        if (! $payload) {
            return $this->unsupported();
        }

        $status = match ($payload['status']) {
            'delivered' => 1,
            'returned' => 7,
            default => null,
        };

        if ($status) {
            $this->updateOrderStatus('redx_tracking_id', $payload['tracking_number'], ['status' => $status]);
        }
    }

    public function carrybee(Request $request)
    {
        if ($request->event === 'webhook.integration') {
            return response()->json(status: 202)
                ->header('X-CB-Webhook-Integration-Header', self::CARRYBEE_SIGNATURE)
                ->header('Accept', 'application/json')
                ->header('Content-Type', 'application/json')
                ->header('Content-Length', 185);
        }

        $payload = $this->decodePayload($request, 'callback.txt');
        if (! $payload) {
            return $this->unsupported();
        }

        match ($payload['event']) {
            'order.picked', 'order.pickup-requested' => $this->updateCarryBeeOrder($payload, 'Order Created'),
            'order.pickup-cancelled' => $this->updateCarryBeeOrder($payload, 'Pickup Cancelled'),
            'order.delivered' => $this->updateOrderStatus('carrybee_consignment_id', $payload['consignment_id'], ['status' => OrderStatus::Delivered->value]),
            'order.returned' => $this->updateOrderStatus('carrybee_consignment_id', $payload['consignment_id'], ['status' => OrderStatus::PendingReturn->value]),
            default => null,
        };

        return response()->json(status: 202)
            ->header('X-CB-Webhook-Integration-Header', self::CARRYBEE_SIGNATURE)
            ->header('Accept', 'application/json')
            ->header('Content-Type', 'application/json')
            ->header('Content-Length', 185);
    }

    private function processPathaoEvent(array $object): void
    {
        $updates = match ($object['event']) {
            'order.created' => [
                'courier_status' => 'Order Created',
                'pathao_consignment_id' => $object['consignment_id'],
                'courier_api_response' => null,
            ],
            'order.updated', 'order.pickup-requested', 'order.assigned-for-pickup',
            'order.picked', 'order.pickup-failed', 'order.pickup-cancelled',
            'order.received-at-last-mile-hub', 'order.assigned-for-delivery' => [
                'courier_status' => $object['order_status'],
            ],
            'order.at-the-sorting-hub', 'order.in-transit' => [
                'courier_status' => $object['order_status'],
                'status' => 6,
            ],
            'order.delivered' => [
                'status' => 1,
                'courier_status' => $object['order_status'],
            ],
            'order.partial-delivery', 'order.delivery-failed', 'order.on-hold', 'order.paid-return', 'order.exchanged' => [
                'courier_status' => $object['order_status'],
                'courier_status_reason' => $object['reason'] ?? null,
            ],
            'order.returned' => [
                'status' => 7,
                'courier_status' => $object['order_status'],
                'courier_status_reason' => $object['reason'] ?? null,
            ],
            default => null,
        };

        if ($updates) {
            $idField = $object['event'] == 'order.created' ? 'invoice_id' : 'pathao_consignment_id';
            $idValue = $object['event'] == 'order.created' ? $object['merchant_order_id'] : $object['consignment_id'];

            $this->updateOrderStatus($idField, $idValue, $updates);
        }
    }

    private function updateCarryBeeOrder(array $object, string $status): void
    {
        $this->logPayload('callbacks.txt', $object);
        $this->updateOrderStatus('invoice_id', $object['merchant_order_id'], [
            'courier_status' => $status,
            'carrybee_consignment_id' => $object['consignment_id'],
        ]);
    }

    private function decodePayload(Request $request, ?string $logFile = null): ?array
    {
        $content = $request->getContent();
        $decoded = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        if ($logFile) {
            $this->logPayload($logFile, $decoded);
        }

        return $decoded;
    }

    private function unsupported()
    {
        return response('', 415);
    }

    private function logPayload(string $file, array $payload): void
    {
        file_put_contents(base_path($file), json_encode($payload));
    }

    private function updateOrderStatus(string $field, string $value, array $updates): void
    {
        DB::table('orders')->where($field, $value)->update($updates);
    }
}
