<?php

namespace App\Http\Controllers\BackEnd;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReceiveReturnOrderRequest;
use App\Models\Order;
use App\Models\ReturnReceive;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\View\View;

class ReturnOrderController extends Controller
{
    public function index(ReceiveReturnOrderRequest $request): View|RedirectResponse
    {
        $validated = $request->validated();
        $invoiceId = $validated['invoice_id'] ?? null;

        if (filled($invoiceId)) {
            return $this->receiveReturn($invoiceId);
        }

        $selectedDate = $this->resolveReturnDate($validated['date'] ?? null);
        $orders = $this->returnOrders($selectedDate);

        return view('backEnd.admin.order-return.index', compact('orders', 'selectedDate'));
    }

    public function sessionClear(): RedirectResponse
    {
        ReturnReceive::query()->truncate();

        return to_route('admin.orders.return.receive');
    }

    public function print(Request $request): View
    {
        $selectedDate = $this->resolveReturnDate($request->query('date'));
        $orders = $this->returnOrders($selectedDate);

        return view('backEnd.admin.order-return.print', compact('orders', 'selectedDate'));
    }

    private function returnOrders(?string $date = null)
    {
        $query = ReturnReceive::query()->latest();

        if ($date !== null) {
            $query->whereDate('created_at', $date);
        }

        return $query->get();
    }

    private function resolveReturnDate(?string $date): ?string
    {
        if (! filled($date)) {
            return Date::today()->toDateString();
        }

        try {
            return Date::parse($date)->toDateString();
        } catch (\Throwable $e) {
            return Date::today()->toDateString();
        }
    }

    private function receiveReturn(string $invoiceId): RedirectResponse
    {
        $order = $this->eligibleOrder($invoiceId);
        if (! $order) {
            return back()->with(['error' => 'No Order Found!']);
        }

        $existsInList = ReturnReceive::query()->where('invoice_no', $invoiceId)->exists();
        if ($existsInList) {
            return back()->with(['error' => 'Already received!']);
        }

        $order->update([
            'status' => OrderStatus::Return->value,
            'return_received_at' => now(),
        ]);

        ReturnReceive::query()->updateOrCreate(
            ['invoice_no' => $invoiceId],
            $this->returnPayload($order)
        );

        return back()->with(['success' => 'Order return received successfully']);
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

    private function returnPayload(Order $order): array
    {
        return [
            'customer_name' => $order->customer_name,
            'customer_phone' => $order->customer_phone,
            'customer_address' => $order->customer_address,
            'total' => $order->total,
        ];
    }
}
