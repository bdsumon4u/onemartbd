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

        $selectedDate = $this->resolveHandoverDate($validated['date'] ?? null);
        $orders = $this->handoverOrders($selectedDate);

        return view('backEnd.admin.parcel-handover.index', compact('orders', 'selectedDate'));
    }

    public function clear(): RedirectResponse
    {
        ParcelHandover::query()->truncate();

        return to_route('admin.orders.parcel.handover');
    }

    public function print(Request $request): View
    {
        $selectedDate = $this->resolveHandoverDate($request->query('date'));
        $orders = $this->handoverOrders($selectedDate);

        return view('backEnd.admin.parcel-handover.print', compact('orders', 'selectedDate'));
    }

    private function handoverOrders(?string $date = null)
    {
        $query = ParcelHandover::query()->latest();

        if ($date !== null) {
            $query->whereDate('created_at', $date);
        }

        return $query->get();
    }

    private function resolveHandoverDate(?string $date): ?string
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
