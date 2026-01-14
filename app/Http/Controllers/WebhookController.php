<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebhookController extends Controller
{
    public function pathao(Request $request)
    {
        $json = file_get_contents('php://input');
        $object = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            exit(header('HTTP/1.0 415 Unsupported Media Type'));
        }

        if ($object['event'] == 'webhook_integration') {
            return response()->json([
                'status' => 'accepted',
                'message' => 'Webhook received successfully',
            ], 202)->header('X-Pathao-Merchant-Webhook-Integration-Secret', 'f3992ecc-59da-4cbe-a049-a13da2018d51');
        }

        $pathao_settings = DB::table('pathao_apis')->select('id', 'store_id')->first();

        if ($object['store_id'] == $pathao_settings->store_id) {
            $this->processPathaoEvent($object);
        }

        return response()->json()->header('X-Pathao-Merchant-Webhook-Integration-Secret', 'f3992ecc-59da-4cbe-a049-a13da2018d51');
    }

    public function redx(Request $request)
    {
        $json = file_get_contents('php://input');
        $object = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            exit(header('HTTP/1.0 415 Unsupported Media Type'));
        }

        $status = match ($object['status']) {
            'delivered' => 1,
            'returned' => 7,
            default => null,
        };

        if ($status) {
            DB::table('orders')->where('redx_tracking_id', $object['tracking_number'])->update(['status' => $status]);
        }
    }

    public function carrybee(Request $request)
    {
        $json = file_get_contents('php://input');
        file_put_contents(base_path('callback.txt'), $json);
        $object = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            exit(header('HTTP/1.0 415 Unsupported Media Type'));
        }

        match ($object['order_status_slug']) {
            'Picked' => $this->updateCarryBeeOrder($object, 'Order Created'),
            'Pickup_Requested' => $this->updateCarryBeeOrder($object, 'Order Created'),
            'Delivered' => DB::table('orders')->where('carrybee_consignment_id', $object['consignment_id'])->update(['status' => 1]),
            'Return' => DB::table('orders')->where('carrybee_consignment_id', $object['consignment_id'])->update(['status' => 8]),
            default => null,
        };

        return response()->json()
            ->header('X-BEE-Signature', 'vN3In6FmNY01M2Vjc3n')
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

            DB::table('orders')->where($idField, $idValue)->update($updates);
        }
    }

    private function updateCarryBeeOrder(array $object, string $status): void
    {
        file_put_contents(base_path('callbacks.txt'), json_encode($object));
        DB::table('orders')->where('invoice_id', $object['merchant_order_id'])->update([
            'courier_status' => $status,
            'carrybee_consignment_id' => $object['consignment_id'],
        ]);
    }
}
