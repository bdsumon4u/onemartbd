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

        [$range, $startDate, $endDate] = $this->resolveReturnRange(
            $validated['range'] ?? null,
            $validated['start_date'] ?? null,
            $validated['end_date'] ?? null
        );
        $orders = $this->returnOrders($startDate, $endDate);

        return view('backEnd.admin.order-return.index', [
            'orders' => $orders,
            'range' => $range,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    public function sessionClear(): RedirectResponse
    {
        ReturnReceive::query()->truncate();

        return to_route('admin.orders.return.receive');
    }

    public function print(Request $request): View
    {
        [$range, $startDate, $endDate] = $this->resolveReturnRange(
            $request->query('range'),
            $request->query('start_date'),
            $request->query('end_date')
        );
        $orders = $this->returnOrders($startDate, $endDate);

        return view('backEnd.admin.order-return.print', compact('orders'));
    }

    private function returnOrders(?string $startDate = null, ?string $endDate = null)
    {
        $query = ReturnReceive::query()->latest();

        if ($startDate !== null && $endDate !== null) {
            $query->whereBetween('created_at', [
                $startDate.' 00:00:00',
                $endDate.' 23:59:59',
            ]);
        }

        return $query->get();
    }

    private function resolveReturnRange(?string $range, ?string $startDate, ?string $endDate): array
    {
        $today = Date::today();

        if (! filled($range)) {
            $range = 'today';
        }

        switch ($range) {
            case 'yesterday':
                $from = $today->subDay();
                $to = $today->subDay();

                break;
            case 'last_3_days':
                $from = $today->subDays(2);
                $to = $today;

                break;
            case 'last_month':
                $from = $today->subMonthNoOverflow()->startOfMonth();
                $to = $today->subMonthNoOverflow()->endOfMonth();

                break;
            case 'this_month':
                $from = $today->startOfMonth();
                $to = $today;

                break;
            case 'last_3_months':
                $from = $today->subMonthsNoOverflow(2)->startOfMonth();
                $to = $today;

                break;
            case 'last_6_months':
                $from = $today->subMonthsNoOverflow(5)->startOfMonth();
                $to = $today;

                break;
            case 'custom':
                try {
                    $from = filled($startDate) ? Date::parse($startDate) : $today;
                } catch (\Throwable $e) {
                    $from = $today;
                }

                try {
                    $to = filled($endDate) ? Date::parse($endDate) : $from;
                } catch (\Throwable $e) {
                    $to = $from;
                }

                break;
            case 'today':
            default:
                $from = $today;
                $to = $today;

                break;
        }

        return [
            $range,
            $from->toDateString(),
            $to->toDateString(),
        ];
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
