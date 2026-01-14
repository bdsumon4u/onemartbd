<?php

namespace App\Http\Controllers\BackEnd;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReceiveReturnOrderRequest;
use App\Models\Order;

class ReturnOrderController extends Controller
{
    public function index(ReceiveReturnOrderRequest $request)
    {
        $invoiceId = $request->input('invoice_id');
        if (! $invoiceId) {
            return view('backEnd.admin.order-return.index');
        }

        $order = $this->eligibleOrder($invoiceId);
        if (! $order) {
            return back()->with(['error' => 'No Order Found!']);
        }

        $order->update([
            'status' => OrderStatus::Return->value,
            'return_received_at' => now(),
        ]);

        session()->push('return_received_orders', $invoiceId);

        return back()->with(['success' => 'Order return received successfully']);
    }

    public function sessionClear()
    {
        session()->forget('return_received_orders');

        return to_route('admin.orders.return.receive');
    }

    private function eligibleOrder(string $invoiceId): ?Order
    {
        return Order::query()
            ->where('invoice_id', $invoiceId)
            ->where(function ($query): void {
                foreach (OrderStatus::preReturnStages() as $status) {
                    $query->orWhere('status', $status->value);
                }
            })
            ->first();
    }
}
