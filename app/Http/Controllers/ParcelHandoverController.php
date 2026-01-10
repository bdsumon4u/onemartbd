<?php

namespace App\Http\Controllers;

use App\Order;
use App\ParcelHandover;
use Illuminate\Http\Request;

class ParcelHandoverController extends Controller
{
    // index
    public function index(Request $request)
    {
        if ($request->input('invoice_id')) {
            $invoiceId = $request->input('invoice_id');
            $order = Order::where('invoice_id', $invoiceId)->first();
            if ($order && $order->status != 8) {
                $order->update([
                    'status' => 8,
                    'handover_date' => now(),
                ]);
                ParcelHandover::create([
                    'invoice_no' => $invoiceId,
                    'customer_name' => $order->customer_name,
                    'customer_phone' => $order->customer_phone,
                    'customer_address' => $order->customer_address,
                    'total' => $order->total,
                ]);

                return back()->with(['success' => 'Order Handover successfully']);
            } else {
                return back()->with(['error' => 'Already Handover!']);
            }
        }
        $orders = ParcelHandover::orderBy('id', 'desc')->get();

        return view('backEnd.admin.parcel-handover.index', compact('orders'));
    }

    // sessionClear
    public function clear()
    {
        ParcelHandover::truncate();

        return to_route('admin.orders.parcel.handover');

    }

    public function print()
    {
        $orders = ParcelHandover::orderBy('id', 'desc')->get();

        return view('backEnd.admin.parcel-handover.print', compact('orders'));
    }
}
