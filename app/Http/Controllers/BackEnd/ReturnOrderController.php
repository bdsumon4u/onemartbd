<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class ReturnOrderController extends Controller
{
    public function index(Request $request)
    {

        $data = [];
        if ($request->input('invoice_id')) {
            $invoiceId = $request->input('invoice_id');
            $order = Order::where('invoice_id', $invoiceId)->first();

            if ($order && ($order->status == 7 || $order->status == 8 || $order->status == 6)) {
                $order->update([
                    'status' => 11,
                    'return_received_at' => now(),
                ]);

                // Check if session has 'data' and retrieve it
                if (session()->has('return_received_orders')) {
                    $data = session()->get('return_received_orders');
                }

                // Ensure $data is an array before pushing
                if (! is_array($data)) {
                    $data = [];
                }

                array_push($data, $invoiceId);
                session()->put('return_received_orders', $data);

                // Debugging session data
                // dd(session()->all());

                return back()->with(['success' => 'Order return received successfully']);
            } else {
                return back()->with(['error' => 'No Order Found!']);
            }
        }

        return view('backEnd.admin.order-return.index');
    }

    public function sessionClear()
    {
        // dd(session()->all());
        session()->forget('return_received_orders');

        return to_route('admin.orders.return.receive');
    }
}
