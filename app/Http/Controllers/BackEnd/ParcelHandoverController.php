<?php

namespace App\Http\Controllers\BackEnd;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ParcelHandoverRequest;
use App\Models\Order;
use App\Models\ParcelHandover;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\View\View;

class ParcelHandoverController extends Controller
{
    public function index(ParcelHandoverRequest $request): View|RedirectResponse
    {
        $validated = $request->validated();
        $invoiceId = $validated['invoice_id'] ?? null;

        if (filled($invoiceId)) {
            return $this->handover($invoiceId);
        }

        [$range, $startDate, $endDate] = $this->resolveHandoverRange(
            $validated['range'] ?? null,
            $validated['start_date'] ?? null,
            $validated['end_date'] ?? null
        );
        $orders = $this->handoverOrders($startDate, $endDate);

        return view('backEnd.admin.parcel-handover.index', [
            'orders' => $orders,
            'range' => $range,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    public function clear(): RedirectResponse
    {
        ParcelHandover::query()->truncate();

        return to_route('admin.orders.parcel.handover');
    }

    public function print(Request $request): View
    {
        [$range, $startDate, $endDate] = $this->resolveHandoverRange(
            $request->query('range'),
            $request->query('start_date'),
            $request->query('end_date')
        );
        $orders = $this->handoverOrders($startDate, $endDate);

        return view('backEnd.admin.parcel-handover.print', compact('orders'));
    }

    private function handoverOrders(?string $startDate = null, ?string $endDate = null)
    {
        $query = ParcelHandover::query()->latest();

        if ($startDate !== null && $endDate !== null) {
            $query->whereBetween('created_at', [
                $startDate.' 00:00:00',
                $endDate.' 23:59:59',
            ]);
        }

        return $query->get();
    }

    private function resolveHandoverRange(?string $range, ?string $startDate, ?string $endDate): array
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

    private function handover(string $invoiceId): RedirectResponse
    {
        $order = Order::query()->where('invoice_id', $invoiceId)->first();

        if (! $order) {
            return back()->with('error', 'Invoice not found.');
        }

        $existsInList = ParcelHandover::query()->where('invoice_no', $invoiceId)->exists();

        if ((int) $order->status === OrderStatus::Courier->value && $existsInList) {
            return back()->with('error', 'Already Handover!');
        }

        if ((int) $order->status !== OrderStatus::Courier->value) {
            $order->update([
                'status' => OrderStatus::Courier->value,
                'handover_date' => now(),
            ]);
        }

        ParcelHandover::query()->updateOrCreate(
            ['invoice_no' => $invoiceId],
            $this->handoverPayload($order)
        );

        return back()->with('success', 'Order Handover successfully');
    }

    private function handoverPayload(Order $order): array
    {
        return [
            'customer_name' => $order->customer_name,
            'customer_phone' => $order->customer_phone,
            'customer_address' => $order->customer_address,
            'total' => $order->total,
        ];
    }
}
