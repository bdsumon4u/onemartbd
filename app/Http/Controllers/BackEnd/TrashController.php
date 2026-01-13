<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderAssign;
use App\Models\OrderProduct;

class TrashController extends Controller
{
    public function index()
    {

        $data['orders'] = Order::with('get_products.get_product', 'get_courier', 'get_assigned.get_employee')
            ->select('source', 'courier_api_response', 'courier_status_reason', 'customer_activity', 'is_fake', 'invoice_id', 'customer_name', 'customer_phone', 'customer_address', 'total', 'order_date', 'created_at', 'status', 'staff_note', 'courier_note', 'courier_status', 'id', 'ip_address', 'courier_id', 'paid', 'due', 'pathao_consignment_id', 'redx_tracking_id', 'payment_status')
            ->orderBy('id', 'desc')->onlyTrashed()->latest()->paginate(10);

        return view('backEnd.admin.trash.index', compact('data'));
    }

    public function restore($id)
    {
        $order = Order::withTrashed()->find($id);
        if ($order) {
            $order->restore();

            return back()->with('success', 'Order restored successfully');
        }

    }

    public function forceDelete($id)
    {
        $order = Order::withTrashed()->find($id);
        if ($order) {
            OrderProduct::where('order_id', $id)->delete();
            OrderAssign::where('order_id', $id)->delete();
            $order->forceDelete();

            return back()->with('success', 'Order deleted successfully');
        }
    }
}
