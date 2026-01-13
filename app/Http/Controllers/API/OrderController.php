<?php

namespace App\Http\Controllers\API;

use App\Models\Attribute;
use App\Models\AttributeItem;
use App\Services\BanglaToEnglishConverter;
use App\Models\Courier;
use App\Models\CourierCity;
use App\Models\CourierZone;
use App\Models\Employee;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderAssign;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\User;
use App\Models\WebSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $access_token = DB::table('web_settings')->select('api_access_token')->where('id', 1)->first()->api_access_token;
        abort_if($access_token != $request->bearerToken(), 403, 'Unauthorized Access');

        if ($request->input('paginate') != null) {
            $paginate = $request->input('paginate');
        } else {
            $paginate = 20;
        }

        // dd($request->all());
        // return $request->all();
        // $courier_id = $request->input('courier_id') ?? null;
        $query = $request->input('query') ?? null;
        $status = $request->input('status');

        $custom_range = $request->input('custom_range');

        $emp_id = DB::table('employees')->where('p_id', $request->input('emp_id'))->select('id')->first();
        $emp_id = $emp_id ? $emp_id->id : '';

        if ($status == 'Processing') {
            $sts = 2;
        } elseif ($status == 'No Response') {
            $sts = 9;
        } elseif ($status == 'Hold') {
            $sts = 0;
        } elseif ($status == 'Pending Payment') {
            $sts = 3;
        } elseif ($status == 'Canceled') {
            $sts = 4;
        } elseif ($status == 'Confirmed') {
            $sts = 13;
        } elseif ($status == 'Pending Invoiced') {
            $sts = 5;
        } elseif ($status == 'Invoiced') {
            $sts = 10;
        } elseif ($status == 'Stock Out') {
            $sts = 14;
        } elseif ($status == 'Courier') {
            $sts = 8;
        } elseif ($status == 'On Delivery') {
            $sts = 6;
        } elseif ($status == 'Delivered') {
            $sts = 1;
        } elseif ($status == 'Partial Delivery') {
            $sts = 15;
        } elseif ($status == 'Pending Returned') {
            $sts = 7;
        } elseif ($status == 'Returned') {
            $sts = 11;
        } elseif ($status == 'Lost') {
            $sts = 8;
        } elseif ($status == null) {
            $sts = null;
        } else {
            $sts = 'All';
        }
        if ($request->input('role_id') == 1 || $request->input('role_id') == 2 || $request->input('role_id') == 3) {
            $data['total_order'] = DB::table('orders')->where('deleted_at', null)->whereBetween('created_at', [\Illuminate\Support\Facades\Date::now()->subDays(3), \Illuminate\Support\Facades\Date::now()]);
            $data['total_process_order'] = DB::table('orders')->where('deleted_at', null)->whereBetween('created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])->where('status', 2);
            $data['total_nr_1_order'] = DB::table('orders')->where('deleted_at', null)->whereBetween('created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])->where('status', 9);
            $data['total_hold_order'] = DB::table('orders')->where('deleted_at', null)->whereBetween('created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])->where('status', 0);
            $data['total_pend_pay_order'] = DB::table('orders')->where('deleted_at', null)->whereBetween('created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])->where('status', 3);
            $data['total_cancel_order'] = DB::table('orders')->where('deleted_at', null)->whereBetween('created_at', [\Illuminate\Support\Facades\Date::now()->subDays(2), \Illuminate\Support\Facades\Date::now()])->where('status', 4);
            $data['total_confirmed_order'] = DB::table('orders')->where('deleted_at', null)->whereBetween('created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])->where('status', 13);
            $data['total_pending_invoiced_order'] = DB::table('orders')->where('deleted_at', null)->whereBetween('created_at', [\Illuminate\Support\Facades\Date::now()->subDays(3), \Illuminate\Support\Facades\Date::now()])->where('status', 5);
            $data['total_invoiced_order'] = DB::table('orders')->where('deleted_at', null)->whereBetween('created_at', [\Illuminate\Support\Facades\Date::now()->subDays(3), \Illuminate\Support\Facades\Date::now()])->where('status', 10);
            $data['total_stockout_order'] = DB::table('orders')->where('deleted_at', null)->whereBetween('created_at', [\Illuminate\Support\Facades\Date::now()->subDays(3), \Illuminate\Support\Facades\Date::now()])->where('status', 14);
            $data['total_courier_order'] = DB::table('orders')->where('deleted_at', null)->whereBetween('created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])->where('status', 8);
            $data['total_on_delivery_order'] = DB::table('orders')->where('deleted_at', null)->whereBetween('created_at', [\Illuminate\Support\Facades\Date::now()->subDays(3), \Illuminate\Support\Facades\Date::now()])->where('status', 6);
            $data['total_deliver_order'] = DB::table('orders')->where('deleted_at', null)->whereBetween('created_at', [\Illuminate\Support\Facades\Date::now()->subDays(2), \Illuminate\Support\Facades\Date::now()])->where('status', 1);
            $data['total_partial_delivery_order'] = DB::table('orders')->where('deleted_at', null)->whereBetween('created_at', [\Illuminate\Support\Facades\Date::now()->subDays(2), \Illuminate\Support\Facades\Date::now()])->where('status', 15);
            $data['total_pending_return_order'] = DB::table('orders')->where('deleted_at', null)->whereBetween('created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])->where('status', 7);
            $data['total_return_order'] = DB::table('orders')->where('deleted_at', null)->whereBetween('created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])->where('status', 11);
            $data['total_lost_order'] = DB::table('orders')->where('deleted_at', null)->whereBetween('created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])->where('status', 16);

            $data['orders'] = Order::query()->whereBetween('created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()]);

            if ($custom_range == 'today') {
                $custom_range = \Illuminate\Support\Facades\Date::today()->toDateTimeString();

                $data['total_order']->whereDate('created_at', $custom_range);
                $data['total_process_order']->whereDate('created_at', $custom_range);
                $data['total_nr_1_order']->whereDate('created_at', $custom_range);
                $data['total_hold_order']->whereDate('created_at', $custom_range);
                $data['total_pend_pay_order']->whereDate('created_at', $custom_range);
                $data['total_cancel_order']->whereDate('created_at', $custom_range);
                $data['total_confirmed_order']->whereDate('created_at', $custom_range);
                $data['total_pending_invoiced_order']->whereDate('created_at', $custom_range);
                $data['total_invoiced_order']->whereDate('created_at', $custom_range);
                $data['total_stockout_order']->whereDate('created_at', $custom_range);
                $data['total_courier_order']->whereDate('created_at', $custom_range);
                $data['total_on_delivery_order']->whereDate('created_at', $custom_range);
                $data['total_deliver_order']->whereDate('created_at', $custom_range);
                $data['total_partial_delivery_order']->whereDate('created_at', $custom_range);
                $data['total_pending_return_order']->whereDate('created_at', $custom_range);
                $data['total_return_order']->whereDate('created_at', $custom_range);
                $data['total_lost_order']->whereDate('created_at', $custom_range);

                $data['orders']->whereDate('created_at', $custom_range);
            } elseif ($custom_range == 'yesterday') {
                $custom_range = \Illuminate\Support\Facades\Date::yesterday()->toDateTimeString();

                $data['total_order']->whereDate('created_at', $custom_range);
                $data['total_process_order']->whereDate('created_at', $custom_range);
                $data['total_nr_1_order']->whereDate('created_at', $custom_range);
                $data['total_hold_order']->whereDate('created_at', $custom_range);
                $data['total_pend_pay_order']->whereDate('created_at', $custom_range);
                $data['total_cancel_order']->whereDate('created_at', $custom_range);
                $data['total_confirmed_order']->whereDate('created_at', $custom_range);
                $data['total_pending_invoiced_order']->whereDate('created_at', $custom_range);
                $data['total_invoiced_order']->whereDate('created_at', $custom_range);
                $data['total_stockout_order']->whereDate('created_at', $custom_range);
                $data['total_courier_order']->whereDate('created_at', $custom_range);
                $data['total_on_delivery_order']->whereDate('created_at', $custom_range);
                $data['total_deliver_order']->whereDate('created_at', $custom_range);
                $data['total_partial_delivery_order']->whereDate('created_at', $custom_range);
                $data['total_pending_return_order']->whereDate('created_at', $custom_range);
                $data['total_return_order']->whereDate('created_at', $custom_range);
                $data['total_lost_order']->whereDate('created_at', $custom_range);

                $data['orders']->whereDate('created_at', $custom_range);
            } elseif ($custom_range == 'last_7_days') {
                $custom_range = \Illuminate\Support\Facades\Date::now()->subDays(7)->toDateTimeString();

                $data['total_order']->where('created_at', '>=', $custom_range);
                $data['total_process_order']->where('created_at', '>=', $custom_range);
                $data['total_nr_1_order']->where('created_at', '>=', $custom_range);
                $data['total_hold_order']->where('created_at', '>=', $custom_range);
                $data['total_pend_pay_order']->where('created_at', '>=', $custom_range);
                $data['total_cancel_order']->where('created_at', '>=', $custom_range);
                $data['total_confirmed_order']->where('created_at', '>=', $custom_range);
                $data['total_pending_invoiced_order']->where('created_at', '>=', $custom_range);
                $data['total_invoiced_order']->where('created_at', '>=', $custom_range);
                $data['total_stockout_order']->where('created_at', '>=', $custom_range);
                $data['total_courier_order']->where('created_at', '>=', $custom_range);
                $data['total_on_delivery_order']->where('created_at', '>=', $custom_range);
                $data['total_deliver_order']->where('created_at', '>=', $custom_range);
                $data['total_partial_delivery_order']->where('created_at', '>=', $custom_range);
                $data['total_pending_return_order']->where('created_at', '>=', $custom_range);
                $data['total_return_order']->where('created_at', '>=', $custom_range);
                $data['total_lost_order']->where('created_at', '>=', $custom_range);

                $data['orders']->where('created_at', '>=', $custom_range);
            } elseif ($custom_range == 'this_month') {
                $custom_range = \Illuminate\Support\Facades\Date::now()->month;

                $data['total_order']->whereMonth('created_at', $custom_range);
                $data['total_process_order']->whereMonth('created_at', $custom_range);
                $data['total_nr_1_order']->whereMonth('created_at', $custom_range);
                $data['total_hold_order']->whereMonth('created_at', $custom_range);
                $data['total_pend_pay_order']->whereMonth('created_at', $custom_range);
                $data['total_cancel_order']->whereMonth('created_at', $custom_range);
                $data['total_confirmed_order']->whereMonth('created_at', $custom_range);
                $data['total_pending_invoiced_order']->whereMonth('created_at', $custom_range);
                $data['total_invoiced_order']->whereMonth('created_at', $custom_range);
                $data['total_stockout_order']->whereMonth('created_at', $custom_range);
                $data['total_courier_order']->whereMonth('created_at', $custom_range);
                $data['total_on_delivery_order']->whereMonth('created_at', $custom_range);
                $data['total_deliver_order']->whereMonth('created_at', $custom_range);
                $data['total_partial_delivery_order']->whereMonth('created_at', $custom_range);
                $data['total_pending_return_order']->whereMonth('created_at', $custom_range);
                $data['total_return_order']->whereMonth('created_at', $custom_range);
                $data['total_lost_order']->whereMonth('created_at', $custom_range);

                $data['orders']->whereMonth('created_at', $custom_range);
            } elseif ($custom_range == 'last_month') {
                $sd = \Illuminate\Support\Facades\Date::now()->subMonth()->startOfMonth()->toDateTimeString();
                $ed = \Illuminate\Support\Facades\Date::now()->subMonth()->endOfMonth()->toDateTimeString();

                $data['total_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_process_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_nr_1_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_hold_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_pend_pay_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_cancel_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_confirmed_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_pending_invoiced_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_invoiced_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_stockout_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_courier_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_on_delivery_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_deliver_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_partial_delivery_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_pending_return_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_return_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_lost_order']->whereBetween('created_at', [$sd, $ed]);

                $data['orders']->whereBetween('created_at', [$sd, $ed]);
            } elseif ($custom_range == 'last_6_months') {
                $sd = \Illuminate\Support\Facades\Date::now()->subMonths(6)->startOfMonth()->toDateTimeString();
                $ed = \Illuminate\Support\Facades\Date::now()->toDateTimeString();

                $data['total_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_process_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_nr_1_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_hold_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_pend_pay_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_cancel_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_confirmed_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_pending_invoiced_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_invoiced_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_stockout_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_courier_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_on_delivery_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_deliver_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_partial_delivery_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_pending_return_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_return_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_lost_order']->whereBetween('created_at', [$sd, $ed]);

                $data['orders']->whereBetween('created_at', [$sd, $ed]);
            } elseif ($request->input('start_date') && $request->input('end_date')) {
                $sd = \Illuminate\Support\Facades\Date::parse($request->input('start_date'))->startOfDay()->toDateTimeString();
                $ed = \Illuminate\Support\Facades\Date::parse($request->input('end_date'))->endOfDay()->toDateTimeString();

                $data['total_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_process_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_nr_1_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_hold_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_pend_pay_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_cancel_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_confirmed_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_pending_invoiced_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_invoiced_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_stockout_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_courier_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_on_delivery_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_deliver_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_partial_delivery_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_pending_return_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_return_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_lost_order']->whereBetween('created_at', [$sd, $ed]);
                $data['orders']->whereBetween('created_at', [$sd, $ed]);
            }

            if ($sts !== null) {
                $data['orders']->where('status', $sts);
            }

            if ($request->input('payment_status') !== null) {
                $data['total_order']->where('payment_status', $request->input('payment_status'));
                $data['total_process_order']->where('payment_status', $request->input('payment_status'));
                $data['total_nr_1_order']->where('payment_status', $request->input('payment_status'));
                $data['total_hold_order']->where('payment_status', $request->input('payment_status'));
                $data['total_pend_pay_order']->where('payment_status', $request->input('payment_status'));
                $data['total_cancel_order']->where('payment_status', $request->input('payment_status'));
                $data['total_confirmed_order']->where('payment_status', $request->input('payment_status'));
                $data['total_pending_invoiced_order']->where('payment_status', $request->input('payment_status'));
                $data['total_invoiced_order']->where('payment_status', $request->input('payment_status'));
                $data['total_stockout_order']->where('payment_status', $request->input('payment_status'));
                $data['total_courier_order']->where('payment_status', $request->input('payment_status'));
                $data['total_on_delivery_order']->where('payment_status', $request->input('payment_status'));
                $data['total_deliver_order']->where('payment_status', $request->input('payment_status'));
                $data['total_partial_delivery_order']->where('payment_status', $request->input('payment_status'));
                $data['total_pending_return_order']->where('payment_status', $request->input('payment_status'));
                $data['total_return_order']->where('payment_status', $request->input('payment_status'));
                $data['total_lost_order']->where('payment_status', $request->input('payment_status'));

                $data['orders']->where('payment_status', $request->input('payment_status'));
            }

            if ($request->input('query')) {
                $data['orders']->where('customer_phone', 'LIKE', "%{$request->input('query')}%");
                $data['orders']->orWhere('customer_name', 'LIKE', "%{$request->input('query')}%");
                $data['orders']->orWhere('invoice_id', 'LIKE', "%{$request->input('query')}%");
                $data['orders']->orWhere('ip_address', $request->input('query'));

                $data['orders']->orWhereHas('get_products', function ($p) use ($query): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('name', 'LIKE', "%{$query}%");
                });
            }

            /*if ($request->input('courier_id')) {
                $data['orders']->where('courier_id', $request->input('courier_id'));
            }*/
            $data['total_order'] = $data['total_order']->count();
            $data['total_process_order'] = $data['total_process_order']->count();
            $data['total_nr_1_order'] = $data['total_nr_1_order']->count();
            $data['total_hold_order'] = $data['total_hold_order']->count();
            $data['total_pend_pay_order'] = $data['total_pend_pay_order']->count();
            $data['total_cancel_order'] = $data['total_cancel_order']->count();
            $data['total_confirmed_order'] = $data['total_confirmed_order']->count();
            $data['total_pending_invoiced_order'] = $data['total_pending_invoiced_order']->count();
            $data['total_invoiced_order'] = $data['total_invoiced_order']->count();
            $data['total_stockout_order'] = $data['total_stockout_order']->count();
            $data['total_courier_order'] = $data['total_courier_order']->count();
            $data['total_on_delivery_order'] = $data['total_on_delivery_order']->count();
            $data['total_deliver_order'] = $data['total_deliver_order']->count();
            $data['total_partial_delivery_order'] = $data['total_partial_delivery_order']->count();
            $data['total_pending_return_order'] = $data['total_pending_return_order']->count();
            $data['total_return_order'] = $data['total_return_order']->count();
            $data['total_lost_order'] = $data['total_lost_order']->count();

            // return $data['total_process_order'];

            $data['count'] = $data['orders']->count();
            $data['orders'] = $data['orders']->with('get_products.get_product', 'get_courier', 'get_assigned.get_employee')
                ->select('invoice_id', 'customer_name', 'customer_phone', 'customer_address', 'total', 'order_date', 'created_at', 'status', 'staff_note', 'courier_note', 'courier_status', 'id', 'ip_address', 'courier_id', 'paid', 'due', 'pathao_consignment_id', 'redx_tracking_id', 'payment_status')
                ->orderBy('id', 'desc')->paginate($paginate);
            foreach ($data['orders'] as $key => $order) {
                $duplicate_orders = DB::table('orders')->where('customer_phone', $order->customer_phone)->count();
                $data['orders'][$key]->duplicate_orders = $duplicate_orders;
            }
            $data['orders']->appends([
                'paginate' => $paginate,
                'query' => $request->input('query'),
                'status' => $request->input('status'),
                'custom_range' => $request->input('custom_range'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
            ]);
        } elseif ($request->input('role_id') == 4) {
            $data['total_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(3), \Illuminate\Support\Facades\Date::now()])
                ->where('order_assigns.employee_id', $emp_id);
            $data['total_process_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])
                ->where([['order_assigns.employee_id', $emp_id], ['status', 2]]);
            $data['total_nr_1_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])
                ->where([['order_assigns.employee_id', $emp_id], ['status', 9]]);
            $data['total_hold_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])
                ->where([['order_assigns.employee_id', $emp_id], ['status', 0]]);
            $data['total_pend_pay_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])
                ->where([['order_assigns.employee_id', $emp_id], ['status', 3]]);
            $data['total_cancel_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(2), \Illuminate\Support\Facades\Date::now()])
                ->where([['order_assigns.employee_id', $emp_id], ['status', 4]]);
            $data['total_confirmed_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(2), \Illuminate\Support\Facades\Date::now()])
                ->where([['order_assigns.employee_id', $emp_id], ['status', 13]]);
            $data['total_pending_invoiced_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(3), \Illuminate\Support\Facades\Date::now()])
                ->where([['order_assigns.employee_id', $emp_id], ['status', 5]]);
            $data['total_invoiced_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(3), \Illuminate\Support\Facades\Date::now()])
                ->where([['order_assigns.employee_id', $emp_id], ['status', 10]]);
            $data['total_stockout_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(3), \Illuminate\Support\Facades\Date::now()])
                ->where([['order_assigns.employee_id', $emp_id], ['status', 14]]);
            $data['total_courier_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(3), \Illuminate\Support\Facades\Date::now()])
                ->where([['order_assigns.employee_id', $emp_id], ['status', 8]]);
            $data['total_on_delivery_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(3), \Illuminate\Support\Facades\Date::now()])
                ->where([['order_assigns.employee_id', $emp_id], ['status', 6]]);
            $data['total_deliver_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(2), \Illuminate\Support\Facades\Date::now()])
                ->where([['order_assigns.employee_id', $emp_id], ['status', 1]]);
            $data['total_partial_delivery_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(2), \Illuminate\Support\Facades\Date::now()])
                ->where([['order_assigns.employee_id', $emp_id], ['status', 15]]);
            $data['total_pending_return_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(2), \Illuminate\Support\Facades\Date::now()])
                ->where([['order_assigns.employee_id', $emp_id], ['status', 7]]);
            $data['total_return_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(2), \Illuminate\Support\Facades\Date::now()])
                ->where([['order_assigns.employee_id', $emp_id], ['status', 11]]);
            $data['total_lost_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(2), \Illuminate\Support\Facades\Date::now()])
                ->where([['order_assigns.employee_id', $emp_id], ['status', 16]]);

            $data['orders'] = Order::query()->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()]);

            if ($custom_range == 'today') {
                $custom_range = \Illuminate\Support\Facades\Date::today()->toDateTimeString();

                $data['total_order']->whereDate('orders.created_at', $custom_range);
                $data['total_process_order']->whereDate('orders.created_at', $custom_range);
                $data['total_nr_1_order']->whereDate('orders.created_at', $custom_range);
                $data['total_hold_order']->whereDate('orders.created_at', $custom_range);
                $data['total_pend_pay_order']->whereDate('orders.created_at', $custom_range);
                $data['total_cancel_order']->whereDate('orders.created_at', $custom_range);
                $data['total_confirmed_order']->whereDate('orders.created_at', $custom_range);
                $data['total_pending_invoiced_order']->whereDate('orders.created_at', $custom_range);
                $data['total_invoiced_order']->whereDate('orders.created_at', $custom_range);
                $data['total_stockout_order']->whereDate('orders.created_at', $custom_range);
                $data['total_courier_order']->whereDate('orders.created_at', $custom_range);
                $data['total_on_delivery_order']->whereDate('orders.created_at', $custom_range);
                $data['total_deliver_order']->whereDate('orders.created_at', $custom_range);
                $data['total_partial_delivery_order']->whereDate('orders.created_at', $custom_range);
                $data['total_pending_return_order']->whereDate('orders.created_at', $custom_range);
                $data['total_return_order']->whereDate('orders.created_at', $custom_range);
                $data['total_lost_order']->whereDate('orders.created_at', $custom_range);

                $data['orders']->whereDate('orders.created_at', $custom_range);
            } elseif ($custom_range == 'yesterday') {
                $custom_range = \Illuminate\Support\Facades\Date::yesterday()->toDateTimeString();

                $data['total_order']->whereDate('orders.created_at', $custom_range);
                $data['total_process_order']->whereDate('orders.created_at', $custom_range);
                $data['total_nr_1_order']->whereDate('orders.created_at', $custom_range);
                $data['total_hold_order']->whereDate('orders.created_at', $custom_range);
                $data['total_pend_pay_order']->whereDate('orders.created_at', $custom_range);
                $data['total_cancel_order']->whereDate('orders.created_at', $custom_range);
                $data['total_confirmed_order']->whereDate('orders.created_at', $custom_range);
                $data['total_pending_invoiced_order']->whereDate('orders.created_at', $custom_range);
                $data['total_invoiced_order']->whereDate('orders.created_at', $custom_range);
                $data['total_stockout_order']->whereDate('orders.created_at', $custom_range);
                $data['total_courier_order']->whereDate('orders.created_at', $custom_range);
                $data['total_on_delivery_order']->whereDate('orders.created_at', $custom_range);
                $data['total_deliver_order']->whereDate('orders.created_at', $custom_range);
                $data['total_partial_delivery_order']->whereDate('orders.created_at', $custom_range);
                $data['total_pending_return_order']->whereDate('orders.created_at', $custom_range);
                $data['total_return_order']->whereDate('orders.created_at', $custom_range);
                $data['total_lost_order']->whereDate('orders.created_at', $custom_range);

                $data['orders']->whereDate('orders.created_at', $custom_range);
            } elseif ($custom_range == 'last_7_days') {
                $custom_range = \Illuminate\Support\Facades\Date::now()->subDays(7)->toDateTimeString();

                $data['total_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_process_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_nr_1_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_hold_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_pend_pay_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_cancel_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_confirmed_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_pending_invoiced_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_invoiced_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_stockout_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_courier_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_on_delivery_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_deliver_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_partial_delivery_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_pending_return_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_return_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_lost_order']->where('orders.created_at', '>=', $custom_range);

                $data['orders']->where('orders.created_at', '>=', $custom_range);
            } elseif ($custom_range == 'this_month') {
                $custom_range = \Illuminate\Support\Facades\Date::now()->month;

                $data['total_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_process_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_nr_1_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_hold_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_pend_pay_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_cancel_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_confirmed_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_pending_invoiced_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_invoiced_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_stockout_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_courier_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_on_delivery_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_deliver_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_partial_delivery_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_pending_return_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_return_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_lost_order']->whereMonth('orders.created_at', $custom_range);

                $data['orders']->whereMonth('orders.created_at', $custom_range);
            } elseif ($custom_range == 'last_month') {
                $sd = \Illuminate\Support\Facades\Date::now()->subMonth()->startOfMonth()->toDateTimeString();
                $ed = \Illuminate\Support\Facades\Date::now()->subMonth()->endOfMonth()->toDateTimeString();

                $data['total_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_process_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_nr_1_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_hold_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_pend_pay_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_cancel_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_confirmed_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_pending_invoiced_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_invoiced_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_stockout_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_courier_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_on_delivery_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_deliver_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_partial_delivery_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_pending_return_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_return_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_lost_order']->whereBetween('orders.created_at', [$sd, $ed]);

                $data['orders']->whereBetween('orders.created_at', [$sd, $ed]);
            } elseif ($custom_range == 'last_6_months') {
                $sd = \Illuminate\Support\Facades\Date::now()->subMonths(6)->startOfMonth()->toDateTimeString();
                $ed = \Illuminate\Support\Facades\Date::now()->toDateTimeString();

                $data['total_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_process_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_nr_1_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_hold_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_pend_pay_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_cancel_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_confirmed_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_pending_invoiced_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_invoiced_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_stockout_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_courier_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_on_delivery_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_deliver_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_partial_delivery_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_pending_return_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_return_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_lost_order']->whereBetween('orders.created_at', [$sd, $ed]);

                $data['orders']->whereBetween('orders.created_at', [$sd, $ed]);
            } elseif ($request->input('start_date') && $request->input('end_date')) {
                $sd = \Illuminate\Support\Facades\Date::parse($request->input('start_date'))->startOfDay()->toDateTimeString();
                $ed = \Illuminate\Support\Facades\Date::parse($request->input('end_date'))->endOfDay()->toDateTimeString();

                $data['total_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_process_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_nr_1_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_hold_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_pend_pay_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_cancel_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_confirmed_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_pending_invoiced_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_invoiced_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_stockout_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_courier_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_on_delivery_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_deliver_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_partial_delivery_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_pending_return_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_return_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_lost_order']->whereBetween('orders.created_at', [$sd, $ed]);

                $data['orders']->whereBetween('orders.created_at', [$sd, $ed]);
            }

            if ($sts !== null) {
                $data['orders']->where('status', $sts);
            }

            if ($request->input('query')) {
                $data['orders']->where('customer_phone', 'LIKE', "%{$request->input('query')}%");
                $data['orders']->orWhere('customer_name', 'LIKE', "%{$request->input('query')}%");
                $data['orders']->orWhere('invoice_id', $request->input('query'));
                $data['orders']->orWhere('ip_address', $request->input('query'));

                $data['orders']->orWhereHas('get_products', function ($p) use ($query): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('name', 'LIKE', "%{$query}%");
                });
            }

            if ($request->input('courier_id')) {
                $data['orders']->where('courier_id', $request->input('courier_id'));
            }

            $data['total_order'] = $data['total_order']->count();
            $data['total_process_order'] = $data['total_process_order']->count();
            $data['total_nr_1_order'] = $data['total_nr_1_order']->count();
            $data['total_hold_order'] = $data['total_hold_order']->count();
            $data['total_pend_pay_order'] = $data['total_pend_pay_order']->count();
            $data['total_cancel_order'] = $data['total_cancel_order']->count();
            $data['total_confirmed_order'] = $data['total_confirmed_order']->count();
            $data['total_pending_invoiced_order'] = $data['total_pending_invoiced_order']->count();
            $data['total_invoiced_order'] = $data['total_invoiced_order']->count();
            $data['total_stockout_order'] = $data['total_stockout_order']->count();
            $data['total_courier_order'] = $data['total_courier_order']->count();
            $data['total_on_delivery_order'] = $data['total_on_delivery_order']->count();
            $data['total_deliver_order'] = $data['total_deliver_order']->count();
            $data['total_partial_delivery_order'] = $data['total_partial_delivery_order']->count();
            $data['total_pending_return_order'] = $data['total_pending_return_order']->count();
            $data['total_return_order'] = $data['total_return_order']->count();
            $data['total_lost_order'] = $data['total_lost_order']->count();

            $data['count'] = $data['orders']->whereHas('get_assigned', function ($qry) use ($emp_id): void {
                $qry->where('employee_id', $emp_id);
            })->count();

            $data['orders'] = $data['orders']->with('get_products.get_product', 'get_courier', 'get_assigned.get_employee')
                ->select('invoice_id', 'customer_name', 'customer_phone', 'customer_address', 'total', 'order_date', 'created_at', 'status', 'staff_note', 'courier_note', 'courier_status', 'id', 'ip_address', 'courier_id', 'paid', 'due', 'pathao_consignment_id', 'redx_tracking_id', 'payment_status')
                ->whereHas('get_assigned', function ($qry) use ($emp_id): void {
                    $qry->where('employee_id', $emp_id);
                })->orderBy('id', 'desc')->paginate($paginate);
            foreach ($data['orders'] as $key => $order) {
                $duplicate_orders = DB::table('orders')->where('customer_phone', $order->customer_phone)->count();
                $data['orders'][$key]->duplicate_orders = $duplicate_orders;
            }
            $data['orders']->appends([
                'paginate' => $paginate,
                'query' => $request->input('query'),
                'status' => $request->input('status'),
                'custom_range' => $request->input('custom_range'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
            ]);
        }
        // dd($data['orders']);

        return response()->json($data);
    }

    public function archiveOrders(Request $request)
    {
        $access_token = DB::table('web_settings')->select('api_access_token')->where('id', 1)->first()->api_access_token;
        abort_if($access_token != $request->bearerToken(), 403, 'Unauthorized Access');

        if ($request->input('paginate') != null) {
            $paginate = $request->input('paginate');
        } else {
            $paginate = 20;
        }

        // dd($request->all());
        // return $request->all();
        // $courier_id = $request->input('courier_id') ?? null;
        $query = $request->input('query') ?? null;
        $status = $request->input('status');

        $custom_range = $request->input('custom_range');

        $emp_id = DB::table('employees')->where('p_id', $request->input('emp_id'))->select('id')->first();
        $emp_id = $emp_id ? $emp_id->id : '';

        if ($status == 'Processing') {
            $sts = 2;
        } elseif ($status == 'Pending Payment') {
            $sts = 3;
        } elseif ($status == 'On Hold') {
            $sts = 0;
        } elseif ($status == 'Canceled') {
            $sts = 4;
        } elseif ($status == 'Delivered') {
            $sts = 1;
        } elseif ($status == 'Pending Delivery') {
            $sts = 5;
        } elseif ($status == 'On Delivery') {
            $sts = 6;
        } elseif ($status == 'Returned') {
            $sts = 7;
        } elseif ($status == 'Courier Hold') {
            $sts = 8;
        } elseif ($status == 'No Response 1') {
            $sts = 9;
        } elseif ($status == 'No Response 2') {
            $sts = 10;
        } elseif ($status == null) {
            $sts = null;
        } else {
            $sts = 'All';
        }
        if ($request->input('role_id') == 1 || $request->input('role_id') == 2 || $request->input('role_id') == 3) {
            $data['total_order'] = DB::table('orders');
            $data['total_hold_order'] = DB::table('orders')->where('status', 0);
            $data['total_deliver_order'] = DB::table('orders')->where('status', 1);
            $data['total_process_order'] = DB::table('orders')->where('status', 2);
            $data['total_pend_pay_order'] = DB::table('orders')->where('status', 3);
            $data['total_cancel_order'] = DB::table('orders')->where('status', 4);
            $data['total_pending_delivery_order'] = DB::table('orders')->where('status', 5);
            $data['total_on_delivery_order'] = DB::table('orders')->where('status', 6);
            $data['total_return_order'] = DB::table('orders')->where('status', 7);
            $data['total_courier_hold_order'] = DB::table('orders')->where('status', 8);
            $data['total_nr_1_order'] = DB::table('orders')->where('status', 9);
            $data['total_nr_2_order'] = DB::table('orders')->where('status', 10);

            $data['orders'] = Order::query();

            if ($custom_range == 'today') {
                $custom_range = \Illuminate\Support\Facades\Date::today()->toDateTimeString();

                $data['total_order']->whereDate('created_at', $custom_range);
                $data['total_hold_order']->whereDate('created_at', $custom_range);
                $data['total_deliver_order']->whereDate('created_at', $custom_range);
                $data['total_process_order']->whereDate('created_at', $custom_range);
                $data['total_pend_pay_order']->whereDate('created_at', $custom_range);
                $data['total_cancel_order']->whereDate('created_at', $custom_range);
                $data['total_pending_delivery_order']->whereDate('created_at', $custom_range);
                $data['total_on_delivery_order']->whereDate('created_at', $custom_range);
                $data['total_return_order']->whereDate('created_at', $custom_range);
                $data['total_courier_hold_order']->whereDate('created_at', $custom_range);
                $data['total_nr_1_order']->whereDate('created_at', $custom_range);
                $data['total_nr_2_order']->whereDate('created_at', $custom_range);

                $data['orders']->whereDate('created_at', $custom_range);
            } elseif ($custom_range == 'yesterday') {
                $custom_range = \Illuminate\Support\Facades\Date::yesterday()->toDateTimeString();

                $data['total_order']->whereDate('created_at', $custom_range);
                $data['total_hold_order']->whereDate('created_at', $custom_range);
                $data['total_deliver_order']->whereDate('created_at', $custom_range);
                $data['total_process_order']->whereDate('created_at', $custom_range);
                $data['total_pend_pay_order']->whereDate('created_at', $custom_range);
                $data['total_cancel_order']->whereDate('created_at', $custom_range);
                $data['total_pending_delivery_order']->whereDate('created_at', $custom_range);
                $data['total_on_delivery_order']->whereDate('created_at', $custom_range);
                $data['total_return_order']->whereDate('created_at', $custom_range);
                $data['total_courier_hold_order']->whereDate('created_at', $custom_range);
                $data['total_nr_1_order']->whereDate('created_at', $custom_range);
                $data['total_nr_2_order']->whereDate('created_at', $custom_range);

                $data['orders']->whereDate('created_at', $custom_range);
            } elseif ($custom_range == 'last_7_days') {
                $custom_range = \Illuminate\Support\Facades\Date::now()->subDays(7)->toDateTimeString();

                $data['total_order']->where('created_at', '>=', $custom_range);
                $data['total_hold_order']->where('created_at', '>=', $custom_range);
                $data['total_deliver_order']->where('created_at', '>=', $custom_range);
                $data['total_process_order']->where('created_at', '>=', $custom_range);
                $data['total_pend_pay_order']->where('created_at', '>=', $custom_range);
                $data['total_cancel_order']->where('created_at', '>=', $custom_range);
                $data['total_pending_delivery_order']->where('created_at', '>=', $custom_range);
                $data['total_on_delivery_order']->where('created_at', '>=', $custom_range);
                $data['total_return_order']->where('created_at', '>=', $custom_range);
                $data['total_courier_hold_order']->where('created_at', '>=', $custom_range);
                $data['total_nr_1_order']->where('created_at', '>=', $custom_range);
                $data['total_nr_2_order']->where('created_at', '>=', $custom_range);

                $data['orders']->where('created_at', '>=', $custom_range);
            } elseif ($custom_range == 'this_month') {
                $custom_range = \Illuminate\Support\Facades\Date::now()->month;

                $data['total_order']->whereMonth('created_at', $custom_range);
                $data['total_hold_order']->whereMonth('created_at', $custom_range);
                $data['total_deliver_order']->whereMonth('created_at', $custom_range);
                $data['total_process_order']->whereMonth('created_at', $custom_range);
                $data['total_pend_pay_order']->whereMonth('created_at', $custom_range);
                $data['total_cancel_order']->whereMonth('created_at', $custom_range);
                $data['total_pending_delivery_order']->whereMonth('created_at', $custom_range);
                $data['total_on_delivery_order']->whereMonth('created_at', $custom_range);
                $data['total_return_order']->whereMonth('created_at', $custom_range);
                $data['total_courier_hold_order']->whereMonth('created_at', $custom_range);
                $data['total_nr_1_order']->whereMonth('created_at', $custom_range);
                $data['total_nr_2_order']->whereMonth('created_at', $custom_range);

                $data['orders']->whereMonth('created_at', $custom_range);
            } elseif ($custom_range == 'last_month') {
                $sd = \Illuminate\Support\Facades\Date::now()->subMonth()->startOfMonth()->toDateTimeString();
                $ed = \Illuminate\Support\Facades\Date::now()->subMonth()->endOfMonth()->toDateTimeString();

                $data['total_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_hold_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_deliver_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_process_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_pend_pay_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_cancel_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_pending_delivery_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_on_delivery_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_return_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_courier_hold_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_nr_1_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_nr_2_order']->whereBetween('created_at', [$sd, $ed]);

                $data['orders']->whereBetween('created_at', [$sd, $ed]);
            } elseif ($custom_range == 'last_6_months') {
                $sd = \Illuminate\Support\Facades\Date::now()->subMonths(6)->startOfMonth()->toDateTimeString();
                $ed = \Illuminate\Support\Facades\Date::now()->toDateTimeString();

                $data['total_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_hold_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_deliver_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_process_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_pend_pay_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_cancel_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_pending_delivery_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_on_delivery_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_return_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_courier_hold_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_nr_1_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_nr_2_order']->whereBetween('created_at', [$sd, $ed]);

                $data['orders']->whereBetween('created_at', [$sd, $ed]);
            } elseif ($request->input('start_date') && $request->input('end_date')) {
                $sd = \Illuminate\Support\Facades\Date::parse($request->input('start_date'))->startOfDay()->toDateTimeString();
                $ed = \Illuminate\Support\Facades\Date::parse($request->input('end_date'))->endOfDay()->toDateTimeString();

                $data['total_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_hold_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_deliver_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_process_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_pend_pay_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_cancel_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_pending_delivery_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_on_delivery_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_return_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_courier_hold_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_nr_1_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_nr_2_order']->whereBetween('created_at', [$sd, $ed]);

                $data['orders']->whereBetween('created_at', [$sd, $ed]);
            }

            if ($sts !== null) {
                $data['orders']->where('status', $sts);
            }

            if ($request->input('payment_status') !== null) {
                $data['total_order']->where('payment_status', $request->input('payment_status'));
                $data['total_hold_order']->where('payment_status', $request->input('payment_status'));
                $data['total_deliver_order']->where('payment_status', $request->input('payment_status'));
                $data['total_process_order']->where('payment_status', $request->input('payment_status'));
                $data['total_pend_pay_order']->where('payment_status', $request->input('payment_status'));
                $data['total_cancel_order']->where('payment_status', $request->input('payment_status'));
                $data['total_pending_delivery_order']->where('payment_status', $request->input('payment_status'));
                $data['total_on_delivery_order']->where('payment_status', $request->input('payment_status'));
                $data['total_return_order']->where('payment_status', $request->input('payment_status'));
                $data['total_courier_hold_order']->where('payment_status', $request->input('payment_status'));
                $data['total_nr_1_order']->where('payment_status', $request->input('payment_status'));
                $data['total_nr_2_order']->where('payment_status', $request->input('payment_status'));

                $data['orders']->where('payment_status', $request->input('payment_status'));
            }

            if ($request->input('query')) {
                $data['orders']->where('customer_phone', 'LIKE', "%{$request->input('query')}%");
                $data['orders']->orWhere('customer_name', 'LIKE', "%{$request->input('query')}%");
                $data['orders']->orWhere('invoice_id', 'LIKE', "%{$request->input('query')}%");
                $data['orders']->orWhere('ip_address', $request->input('query'));

                $data['orders']->orWhereHas('get_products', function ($p) use ($query): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('name', 'LIKE', "%{$query}%");
                });
            }

            /*if ($request->input('courier_id')) {
                $data['orders']->where('courier_id', $request->input('courier_id'));
            }*/

            $data['total_order'] = $data['total_order']->count();
            $data['total_hold_order'] = $data['total_hold_order']->count();
            $data['total_deliver_order'] = $data['total_deliver_order']->count();
            $data['total_process_order'] = $data['total_process_order']->count();
            $data['total_pend_pay_order'] = $data['total_pend_pay_order']->count();
            $data['total_cancel_order'] = $data['total_cancel_order']->count();
            $data['total_pending_delivery_order'] = $data['total_pending_delivery_order']->count();
            $data['total_on_delivery_order'] = $data['total_on_delivery_order']->count();
            $data['total_return_order'] = $data['total_return_order']->count();
            $data['total_courier_hold_order'] = $data['total_courier_hold_order']->count();
            $data['total_nr_1_order'] = $data['total_nr_1_order']->count();
            $data['total_nr_2_order'] = $data['total_nr_2_order']->count();

            $data['count'] = $data['orders']->count();
            $data['orders'] = $data['orders']->with('get_products.get_product', 'get_courier', 'get_assigned.get_employee')
                ->select('invoice_id', 'customer_name', 'customer_phone', 'customer_address', 'total', 'order_date', 'created_at', 'status', 'staff_note', 'courier_note', 'courier_status', 'id', 'ip_address', 'courier_id', 'paid', 'due', 'pathao_consignment_id', 'redx_tracking_id', 'payment_status')
                ->orderBy('id', 'desc')->paginate($paginate);
            foreach ($data['orders'] as $key => $order) {
                $duplicate_orders = DB::table('orders')->where('customer_phone', $order->customer_phone)->count();
                $data['orders'][$key]->duplicate_orders = $duplicate_orders;
            }
            $data['orders']->appends([
                'paginate' => $paginate,
                'query' => $request->input('query'),
                'status' => $request->input('status'),
                'custom_range' => $request->input('custom_range'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
            ]);
        } elseif ($request->input('role_id') == 4) {
            $data['total_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])
                ->where('order_assigns.employee_id', $emp_id);
            $data['total_hold_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])
                ->where([['order_assigns.employee_id', $emp_id], ['status', 0]]);
            $data['total_deliver_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])
                ->where([['order_assigns.employee_id', $emp_id], ['status', 1]]);
            $data['total_process_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])
                ->where([['order_assigns.employee_id', $emp_id], ['status', 2]]);
            $data['total_pend_pay_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])
                ->where([['order_assigns.employee_id', $emp_id], ['status', 3]]);
            $data['total_cancel_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])
                ->where([['order_assigns.employee_id', $emp_id], ['status', 4]]);
            $data['total_pending_delivery_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])
                ->where([['order_assigns.employee_id', $emp_id], ['status', 5]]);
            $data['total_on_delivery_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])
                ->where([['order_assigns.employee_id', $emp_id], ['status', 6]]);
            $data['total_return_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])
                ->where([['order_assigns.employee_id', $emp_id], ['status', 7]]);
            $data['total_courier_hold_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])
                ->where([['order_assigns.employee_id', $emp_id], ['status', 8]]);
            $data['total_nr_1_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])
                ->where([['order_assigns.employee_id', $emp_id], ['status', 9]]);
            $data['total_nr_2_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])
                ->where([['order_assigns.employee_id', $emp_id], ['status', 10]]);

            $data['orders'] = Order::query()->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()]);

            if ($custom_range == 'today') {
                $custom_range = \Illuminate\Support\Facades\Date::today()->toDateTimeString();

                $data['total_order']->whereDate('orders.created_at', $custom_range);
                $data['total_hold_order']->whereDate('orders.created_at', $custom_range);
                $data['total_deliver_order']->whereDate('orders.created_at', $custom_range);
                $data['total_process_order']->whereDate('orders.created_at', $custom_range);
                $data['total_pend_pay_order']->whereDate('orders.created_at', $custom_range);
                $data['total_cancel_order']->whereDate('orders.created_at', $custom_range);
                $data['total_pending_delivery_order']->whereDate('orders.created_at', $custom_range);
                $data['total_on_delivery_order']->whereDate('orders.created_at', $custom_range);
                $data['total_return_order']->whereDate('orders.created_at', $custom_range);
                $data['total_courier_hold_order']->whereDate('orders.created_at', $custom_range);
                $data['total_nr_1_order']->whereDate('orders.created_at', $custom_range);
                $data['total_nr_2_order']->whereDate('orders.created_at', $custom_range);

                $data['orders']->whereDate('orders.created_at', $custom_range);
            } elseif ($custom_range == 'yesterday') {
                $custom_range = \Illuminate\Support\Facades\Date::yesterday()->toDateTimeString();

                $data['total_order']->whereDate('orders.created_at', $custom_range);
                $data['total_hold_order']->whereDate('orders.created_at', $custom_range);
                $data['total_deliver_order']->whereDate('orders.created_at', $custom_range);
                $data['total_process_order']->whereDate('orders.created_at', $custom_range);
                $data['total_pend_pay_order']->whereDate('orders.created_at', $custom_range);
                $data['total_cancel_order']->whereDate('orders.created_at', $custom_range);
                $data['total_pending_delivery_order']->whereDate('orders.created_at', $custom_range);
                $data['total_on_delivery_order']->whereDate('orders.created_at', $custom_range);
                $data['total_return_order']->whereDate('orders.created_at', $custom_range);
                $data['total_courier_hold_order']->whereDate('orders.created_at', $custom_range);
                $data['total_nr_1_order']->whereDate('orders.created_at', $custom_range);
                $data['total_nr_2_order']->whereDate('orders.created_at', $custom_range);

                $data['orders']->whereDate('orders.created_at', $custom_range);
            } elseif ($custom_range == 'last_7_days') {
                $custom_range = \Illuminate\Support\Facades\Date::now()->subDays(7)->toDateTimeString();

                $data['total_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_hold_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_deliver_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_process_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_pend_pay_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_cancel_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_pending_delivery_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_on_delivery_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_return_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_courier_hold_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_nr_1_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_nr_2_order']->where('orders.created_at', '>=', $custom_range);

                $data['orders']->where('orders.created_at', '>=', $custom_range);
            } elseif ($custom_range == 'this_month') {
                $custom_range = \Illuminate\Support\Facades\Date::now()->month;

                $data['total_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_hold_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_deliver_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_process_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_pend_pay_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_cancel_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_pending_delivery_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_on_delivery_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_return_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_courier_hold_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_nr_1_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_nr_2_order']->whereMonth('orders.created_at', $custom_range);

                $data['orders']->whereMonth('orders.created_at', $custom_range);
            } elseif ($custom_range == 'last_month') {
                $sd = \Illuminate\Support\Facades\Date::now()->subMonth()->startOfMonth()->toDateTimeString();
                $ed = \Illuminate\Support\Facades\Date::now()->subMonth()->endOfMonth()->toDateTimeString();

                $data['total_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_hold_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_deliver_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_process_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_pend_pay_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_cancel_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_pending_delivery_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_on_delivery_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_return_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_courier_hold_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_nr_1_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_nr_2_order']->whereBetween('orders.created_at', [$sd, $ed]);

                $data['orders']->whereBetween('orders.created_at', [$sd, $ed]);
            } elseif ($custom_range == 'last_6_months') {
                $sd = \Illuminate\Support\Facades\Date::now()->subMonths(6)->startOfMonth()->toDateTimeString();
                $ed = \Illuminate\Support\Facades\Date::now()->toDateTimeString();

                $data['total_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_hold_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_deliver_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_process_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_pend_pay_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_cancel_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_pending_delivery_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_on_delivery_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_return_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_courier_hold_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_nr_1_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_nr_2_order']->whereBetween('orders.created_at', [$sd, $ed]);

                $data['orders']->whereBetween('orders.created_at', [$sd, $ed]);
            } elseif ($request->input('start_date') && $request->input('end_date')) {
                $sd = \Illuminate\Support\Facades\Date::parse($request->input('start_date'))->startOfDay()->toDateTimeString();
                $ed = \Illuminate\Support\Facades\Date::parse($request->input('end_date'))->endOfDay()->toDateTimeString();

                $data['total_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_hold_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_deliver_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_process_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_pend_pay_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_cancel_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_pending_delivery_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_on_delivery_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_return_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_courier_hold_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_nr_1_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_nr_2_order']->whereBetween('orders.created_at', [$sd, $ed]);

                $data['orders']->whereBetween('orders.created_at', [$sd, $ed]);
            }

            if ($sts !== null) {
                $data['orders']->where('status', $sts);
            }

            if ($request->input('query')) {
                $data['orders']->where('customer_phone', 'LIKE', "%{$request->input('query')}%");
                $data['orders']->orWhere('customer_name', 'LIKE', "%{$request->input('query')}%");
                $data['orders']->orWhere('invoice_id', $request->input('query'));
                $data['orders']->orWhere('ip_address', $request->input('query'));

                $data['orders']->orWhereHas('get_products', function ($p) use ($query): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('name', 'LIKE', "%{$query}%");
                });
            }

            if ($request->input('courier_id')) {
                $data['orders']->where('courier_id', $request->input('courier_id'));
            }

            $data['total_order'] = $data['total_order']->count();
            $data['total_hold_order'] = $data['total_hold_order']->count();
            $data['total_deliver_order'] = $data['total_deliver_order']->count();
            $data['total_process_order'] = $data['total_process_order']->count();
            $data['total_pend_pay_order'] = $data['total_pend_pay_order']->count();
            $data['total_cancel_order'] = $data['total_cancel_order']->count();
            $data['total_pending_delivery_order'] = $data['total_pending_delivery_order']->count();
            $data['total_on_delivery_order'] = $data['total_on_delivery_order']->count();
            $data['total_return_order'] = $data['total_return_order']->count();
            $data['total_courier_hold_order'] = $data['total_courier_hold_order']->count();
            $data['total_nr_1_order'] = $data['total_nr_1_order']->count();
            $data['total_nr_2_order'] = $data['total_nr_2_order']->count();

            $data['count'] = $data['orders']->whereHas('get_assigned', function ($qry) use ($emp_id): void {
                $qry->where('employee_id', $emp_id);
            })->count();

            $data['orders'] = $data['orders']->with('get_products.get_product', 'get_courier', 'get_assigned.get_employee')
                ->select('invoice_id', 'customer_name', 'customer_phone', 'customer_address', 'total', 'order_date', 'created_at', 'status', 'staff_note', 'courier_note', 'courier_status', 'id', 'ip_address', 'courier_id', 'paid', 'due', 'pathao_consignment_id', 'redx_tracking_id', 'payment_status')
                ->whereHas('get_assigned', function ($qry) use ($emp_id): void {
                    $qry->where('employee_id', $emp_id);
                })->orderBy('id', 'desc')->paginate($paginate);
            foreach ($data['orders'] as $key => $order) {
                $duplicate_orders = DB::table('orders')->where('customer_phone', $order->customer_phone)->count();
                $data['orders'][$key]->duplicate_orders = $duplicate_orders;
            }
            $data['orders']->appends([
                'paginate' => $paginate,
                'query' => $request->input('query'),
                'status' => $request->input('status'),
                'custom_range' => $request->input('custom_range'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
            ]);
        }
        // dd($data['orders']);

        return response()->json($data);
    }

    public function store(Request $request)
    {
        $access_token = DB::table('web_settings')->select('api_access_token')->where('id', 1)->first()->api_access_token;
        abort_if($access_token != $request->bearerToken(), 403, 'Unauthorized Access');

        // dd($request->all());
        // return $request->all();
        $order = Order::count();
        if ($order > 0) {
            $invoice_id = Order::latest('id')->first()->invoice_id;
            $invoice_id = trim((string) $invoice_id, 'INV');
            $invoice_id++;
            $invoice_id = 'INV'.$invoice_id;
        } else {
            $invoice_id = 'INV1';
        }

        // create customer account
        $check_cus = User::where('phone', $request->customer_phone)->first();
        if ($check_cus) {
            $customer_id = $check_cus;
        } else {
            $customer_id = User::create([
                'name' => $request->customer_name,
                'phone' => $request->customer_phone,
                'address' => $request->customer_address,
                'password' => Hash::make($request->customer_phone),
            ]);
        }

        $order_date = \Illuminate\Support\Facades\Date::parse($request->order_date)->format('Y-m-d');
        $inputs = array_merge($request->all(), [
            'invoice_id' => $invoice_id,
            'order_date' => $order_date,
            'customer_id' => $customer_id->id,
            'status' => $request->status,
        ]);

        $order_id = Order::create($inputs);
        // insert products
        foreach ($request->product_id as $key => $item) {
            $attrb = [];
            if ($request->attribute_id) {
                if (array_key_exists($item, $request->attribute_id)) {
                    foreach ($request->attribute_id[$item] as $item2) {
                        $an = Attribute::find($item2)->title;
                        $ain = AttributeItem::find($request->attribute_item_id[$item][$item2][0])->item_title;
                        $attr[0][$an] = $ain;
                    }
                    foreach ($request->attribute_id[$item] as $item2) {
                        $an = $item2;
                        $ain = $request->attribute_item_id[$item][$item2][0];
                        $attr[1][$an] = $ain;
                    }
                    $attrb[0] = json_encode($attr[0]);
                    $attrb[1] = json_encode($attr[1]);
                } else {
                    $attrb = [];
                }
            }
            $price = $request->price[$key] * $request->qty[$key];
            OrderProduct::create([
                'order_id' => $order_id->id,
                'product_id' => $item,
                'qty' => $request->qty[$key],
                'price' => $price,
                'attributes' => count($attrb) > 0 ? $attrb[0] : null,
                'attribute_ids' => count($attrb) > 0 ? $attrb[1] : null,
            ]);
        }

        if ($request->status == 5) {
            if ($request->courier_id == 1) {
                // pathao courier entry
                $credential = DB::table('pathao_apis')->select('is_active', 'access_token', 'store_id')->where('id', 1)->first();
                if ($credential->is_active == 1) {
                    $url = 'https://api-hermes.pathao.com/aladdin/api/v1/orders';
                    $item_description = '';
                    foreach ($order_id->get_products as $get_product) {
                        $item_description .= $get_product->get_product->name."\n";
                    }
                    $curl = curl_init();
                    $vars = [
                        'store_id' => $credential->store_id,
                        'merchant_order_id' => $order_id->invoice_id ?? null,
                        'sender_name' => env('APP_NAME'),
                        // 'sender_phone' => null,
                        'recipient_name' => $order_id->customer_name ?? null,
                        'recipient_phone' => $order_id->customer_phone ?? null,
                        'recipient_address' => $order_id->customer_address ?? null,
                        'recipient_city' => $order_id->courier_city_id ?? null,
                        'recipient_zone' => $order_id->courier_zone_id ?? null,
                        'recipient_area' => null,
                        'delivery_type' => 48,
                        'item_type' => 2,
                        'special_instruction' => null,
                        'item_quantity' => $order_id->get_products->sum('qty') ?? 1,
                        'item_weight' => 0.5,
                        'amount_to_collect' => $order_id->due ?? 0,
                        'item_description' => $item_description ?? null,
                    ];
                    $headers = [
                        'accept: application/json',
                        'content-type: application/json',
                        'authorization: Bearer '.$credential->access_token,
                    ];
                    // dd($vars);
                    $json_string = json_encode($vars);
                    // dd($json_string);
                    curl_setopt($curl, CURLOPT_URL, $url);
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $json_string);
                    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    $data = curl_exec($curl);
                    $data = json_decode($data, true);
                    curl_close($curl);
                    if ($data['code'] != 200) {
                        $date = \Illuminate\Support\Facades\Date::now()."\n";
                        $fp = fopen(base_path('storage/logs/pathao_entry_log.txt'), 'a'); // opens file in append mode
                        fwrite($fp, $date.json_encode($data)."\n\n");
                        fclose($fp);
                    }
                    // dd($data['data']->consignment_id);

                    $order_id->update([
                        'pathao_consignment_id' => $data['code'] == 200 ? $data['data']['consignment_id'] : null,
                    ]);
                }
            } elseif ($request->courier_id == 2) {
                // redx courier entry
                $redx_credential = DB::table('redx_apis')->select('is_active', 'access_token')->where('id', 1)->first();
                if ($redx_credential->is_active == 1) {
                    // get delivery_area
                    $curl = curl_init();

                    curl_setopt_array($curl, [
                        CURLOPT_URL => 'https://openapi.redx.com.bd/v1.0.0-beta/areas',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                        CURLOPT_HTTPHEADER => [
                            'API-ACCESS-TOKEN: Bearer '.$redx_credential->access_token,
                        ],
                    ]);
                    $response = curl_exec($curl);
                    curl_close($curl);
                    $delivery_areas = '';
                    foreach (json_decode($response, true)['areas'] as $delivery_area) {
                        if ($delivery_area['id'] == $order_id->courier_city_id) {
                            $delivery_areas = $delivery_area['name'];
                            break;
                        }
                    }

                    // store order into redx
                    $url = 'https://openapi.redx.com.bd/v1.0.0-beta/parcel';
                    $curl = curl_init();
                    $vars = [
                        'customer_name' => $order_id->customer_name ?? null,
                        'customer_phone' => $order_id->customer_phone ?? null,
                        'delivery_area' => $delivery_areas ?? null,
                        'delivery_area_id' => $order_id->courier_city_id ?? null,
                        'customer_address' => $order_id->customer_address ?? null,
                        'merchant_invoice_id' => $order_id->invoice_id ?? null,
                        'cash_collection_amount' => $order_id->due ?? 0,
                        'parcel_weight' => 500,
                        'instruction' => '',
                        'value' => $order_id->due ?? 0,
                    ];
                    $headers = [
                        'API-ACCESS-TOKEN: Bearer '.$redx_credential->access_token,
                        'Content-Type: application/json',
                    ];
                    $json_string = json_encode($vars);
                    // dd($json_string);
                    curl_setopt_array($curl, [
                        CURLOPT_HTTPHEADER => $headers,
                        CURLOPT_URL => $url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => $json_string,

                    ]);
                    $response = curl_exec($curl);
                    curl_close($curl);

                    $order_id->update([
                        'redx_tracking_id' => json_decode($response, true)['tracking_id'] ?? null,
                    ]);
                }
            }
        }

        // create transaction
        if ($request->input('role_id') == 1 || $request->input('role_id') == 2) {// super admin and admin
            $user_name = $request->input('user_name');
            $user_id = $request->input('user_id');
            $created_by = 'admin';
        } elseif ($request->input('role_id') == 3) {// manager
            $user_name = $request->input('user_name');
            $user_id = $request->input('user_id');
            $created_by = 'manager';
        } elseif ($request->input('role_id') == 4) {// employee
            $user_name = $request->input('user_name');
            $user_id = $request->input('user_id');
            $created_by = 'employee';
        }

        if ($request->input('role_id') == 1 || $request->input('role_id') == 2) {
            $b = Employee::where('status', 1)->get();
            if ($b->count() > 0) {
                foreach ($b as $item) {
                    $i[$item->id] = $item->name;
                }
                $i = array_rand($i);

                OrderAssign::create([
                    'order_id' => $order_id->id,
                    'employee_id' => $i,
                ]);
            }

            $emp = DB::table('employees')->where('id', $i)->select('name')->first();
            // create transaction
            order_transaction(
                'api',
                $order_id->id,
                strtr(config('transaction_texts.new_order'), [
                    '{user_name}' => $user_name,
                    '{role}' => $created_by,
                    '{employee_name}' => $emp->name,
                ]),
                null,
                $created_by,
                $user_id,
                $i
            );

            return true;
        } elseif ($request->input('role_id') == 3) {
            $b = Employee::where('status', 1)->get();
            if ($b->count() > 0) {
                foreach ($b as $item) {
                    $i[$item->id] = $item->name;
                }
                $i = array_rand($i);

                OrderAssign::create([
                    'order_id' => $order_id->id,
                    'employee_id' => $i,
                ]);
            }

            $emp = DB::table('employees')->where('id', $i)->select('name')->first();
            // create transaction
            order_transaction(
                'api',
                $order_id->id,
                strtr(config('transaction_texts.new_order'), [
                    '{user_name}' => $user_name,
                    '{role}' => $created_by,
                    '{employee_name}' => $emp->name,
                ]),
                null,
                $created_by,
                $user_id,
                $i
            );

            return true;
        } elseif ($request->input('role_id') == 4) {
            // $emp = Auth::guard('employee')->user();
            $emp = DB::table('employees')->where('p_id', $request->input('user_id'))->select('id', 'name')->first();
            OrderAssign::create([
                'order_id' => $order_id->id,
                'employee_id' => $emp->id,
            ]);

            // create transaction
            order_transaction(
                'api',
                $order_id->id,
                strtr(config('transaction_texts.new_order'), [
                    '{user_name}' => $user_name,
                    '{role}' => $created_by,
                    '{employee_name}' => $emp->name,
                ]),
                null,
                $created_by,
                $user_id,
                $emp->id
            );

            return true;
        } else {
            return false;
        }

    }

    public function edit(Request $request)
    {
        $access_token = DB::table('web_settings')->select('api_access_token')->where('id', 1)->first()->api_access_token;
        abort_if($access_token != $request->bearerToken(), 403, 'Unauthorized Access');

        $data['products'] = Product::/* where('status', 1)-> */ pluck('name', 'id');
        $data['order'] = Order::with('get_transactions', 'get_products.get_product')->find($request->id);
        $data['sms_body'] = DB::table('web_settings')->where('id', 1)->first()->order_custom_sms;
        $data['courier'] = Courier::where('status', 1)->pluck('courier_name', 'id');
        if ($data['order']->courier_id == 1) { // 1=pathao
            $credential = DB::table('pathao_apis')->select('is_active', 'access_token')->where('id', 1)->first();
            // dd($credential);
            $url = 'https://api-hermes.pathao.com/aladdin/api/v1/countries/1/city-list';
            $curl = curl_init();
            $headers = [
                'accept: application/json',
                'content-type: application/json',
                'Authorization: Bearer '.$credential->access_token,
            ];
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, false);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $d1 = curl_exec($curl);
            $d1 = json_decode($d1, true);
            // curl_close($curl);

            $data1 = [];
            foreach ($d1['data']['data'] as $item) {
                $data1[$item['city_id']] = $item['city_name'];
            }
            $data['courier_city'] = $data1;

            // dd($credential);
            $url = 'https://api-hermes.pathao.com/aladdin/api/v1/cities/'.$data['order']->courier_city_id.'/zone-list';
            // $curl = curl_init();

            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, false);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $d2 = curl_exec($curl);
            $d2 = json_decode($d2, true);
            curl_close($curl);

            $data2 = [];
            foreach ($d2['data']['data'] as $item) {
                $data2[$item['zone_id']] = $item['zone_name'];
            }
            $data['courier_zone'] = $data2;
        } elseif ($data['order']->courier_id == 2) { // 2=redx
            $credential = DB::table('redx_apis')->select('is_active', 'access_token')->where('id', 1)->first();
            $url = 'https://openapi.redx.com.bd/v1.0.0-beta/areas';
            $curl = curl_init();
            $headers = [
                'API-ACCESS-TOKEN: Bearer '.$credential->access_token,
            ];

            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => $headers,
            ]);

            $d1 = curl_exec($curl);
            $d1 = json_decode($d1, true)['areas'];
            curl_close($curl);

            $data1 = [];
            foreach ($d1 as $item) {
                $data1[$item['id']] = $item['division_name'].' > '.$item['district_name'].' > '.$item['name'];
            }
            $data['courier_city'] = $data1;

            $data['courier_zone'] = [];
        } else {
            $data['courier_city'] = CourierCity::where([['status', 1], ['courier_id', $data['order']->courier_id]])->pluck('city_name', 'id');
            $data['courier_zone'] = CourierZone::where([['status', 1], ['courier_id', $data['order']->courier_id]])->pluck('zone_name', 'id');
        }

        return response()->json($data);
    }

    public function update(Request $request)
    {
        $access_token = DB::table('web_settings')->select('api_access_token')->where('id', 1)->first()->api_access_token;
        abort_if($access_token != $request->bearerToken(), 403, 'Unauthorized Access');

        // return $request->all();
        if ($request->product_id) {
            $id = $request->id;
            $order_date = \Illuminate\Support\Facades\Date::parse($request->order_date)->format('Y-m-d');
            $inputs = array_merge($request->all(), [
                'order_date' => $order_date,
                // 'status' => $request->status ?? $request->old_status,
            ]);

            $order_id = Order::find($id);
            $order_id->update($inputs);

            OrderProduct::where('order_id', $id)->delete();
            foreach ($request->product_id as $key => $item) {
                $attrb = [];
                if ($request->attribute_id) {
                    if (array_key_exists($item, $request->attribute_id)) {
                        foreach ($request->attribute_id[$item] as $item2) {
                            $an = Attribute::find($item2)->title;
                            $ain = AttributeItem::find($request->attribute_item_id[$item][$item2][0])->item_title;
                            $attr[0][$an] = $ain;
                        }
                        foreach ($request->attribute_id[$item] as $item2) {
                            $an = $item2;
                            $ain = $request->attribute_item_id[$item][$item2][0];
                            $attr[1][$an] = $ain;
                        }
                        $attrb[0] = json_encode($attr[0]);
                        $attrb[1] = json_encode($attr[1]);
                    } else {
                        $attrb = [];
                    }
                }

                $price = $request->price[$key] * $request->qty[$key];
                OrderProduct::create([
                    'order_id' => $id,
                    'product_id' => $item,
                    'qty' => $request->qty[$key],
                    'price' => $price,
                    'attributes' => count($attrb) > 0 ? $attrb[0] : null,
                    'attribute_ids' => count($attrb) > 0 ? $attrb[1] : null,
                ]);
            }

            // create transaction
            if ($request->input('role_id') == 1 || $request->input('role_id') == 2) {// super admin and admin
                $user_name = $request->input('user_name');
                $user_id = $request->input('user_id');
                $created_by = 'admin';
            } elseif ($request->input('role_id') == 3) {// manager
                $user_name = $request->input('user_name');
                $user_id = $request->input('user_id');
                $created_by = 'manager';
            } elseif ($request->input('role_id') == 4) {// employee
                $user_name = $request->input('user_name');
                $user_id = $request->input('user_id');
                $created_by = 'employee';
            }

            order_transaction(
                'api',
                $id,
                strtr(config('transaction_texts.update_order'), [
                    '{user_name}' => $user_name,
                    '{role}' => $created_by,
                ]),
                null,
                $created_by,
                $user_id,
                null
            );
            $web_settings = DB::table('web_settings')->where('id', 1)->first();
            if ($request->old_status != 5 && $request->status == 5) {
                // send order confirm sms
                if ($web_settings->is_order_confirm_sms == 1) {
                    $mgs_body = $web_settings->order_confirm_sms;
                    $products = '';
                    foreach ($order_id->get_products as $key => $item) {
                        if ($key != 0) {
                            $products .= "\n";
                        }
                        $products .= $item->get_product->name.'.';
                    }
                    $msg = $mgs_body."\n\nOrder No. - ".$order_id->invoice_id."\nProduct(s) - ".$products."\nTotal Price - TK".$order_id->total.' (Inc. Delivery Charge)'.config('default_text.sms_footer');
                    // $text = str_replace(' ', '+', $msg);
                    // $text = urlencode($msg);
                    $text = $msg;

                    $apikey = config('app.sms_api_key');
                    // $sender = config('app.sms_sender');

                    $msisdn = ltrim((string) BanglaToEnglishConverter::bn2en($order_id->customer_phone), '+');
                    // dd($apikey, $msisdn, $text);
                    $curl = curl_init();

                    curl_setopt_array($curl, [
                        CURLOPT_URL => 'https://api.sms.net.bd/sendsms',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => ['api_key' => $apikey, 'msg' => $text, 'to' => $msisdn],
                    ]);

                    $response = curl_exec($curl);

                    curl_close($curl);
                    // dd($response);
                }

                if ($request->courier_id == 1) {
                    // pathao courier entry
                    $credential = DB::table('pathao_apis')->select('is_active', 'access_token', 'store_id')->where('id', 1)->first();
                    if ($credential->is_active == 1) {
                        $url = 'https://api-hermes.pathao.com/aladdin/api/v1/orders';
                        $item_description = '';
                        foreach ($order_id->get_products as $get_product) {
                            $item_description .= $get_product->get_product->name."\n";
                        }
                        $curl = curl_init();
                        $vars = [
                            'store_id' => $credential->store_id,
                            'merchant_order_id' => $order_id->invoice_id ?? null,
                            'sender_name' => env('APP_NAME'),
                            // 'sender_phone' => null,
                            'recipient_name' => $order_id->customer_name ?? null,
                            'recipient_phone' => $order_id->customer_phone ?? null,
                            'recipient_address' => $order_id->customer_address ?? null,
                            'recipient_city' => $order_id->courier_city_id ?? null,
                            'recipient_zone' => $order_id->courier_zone_id ?? null,
                            'recipient_area' => null,
                            'delivery_type' => 48,
                            'item_type' => 2,
                            'special_instruction' => null,
                            'item_quantity' => $order_id->get_products->sum('qty') ?? 1,
                            'item_weight' => 0.5,
                            'amount_to_collect' => $order_id->due ?? 0,
                            'item_description' => $item_description ?? null,
                        ];
                        $headers = [
                            'accept: application/json',
                            'content-type: application/json',
                            'authorization: Bearer '.$credential->access_token,
                        ];
                        // dd($vars);
                        $json_string = json_encode($vars);
                        // dd($json_string);
                        curl_setopt($curl, CURLOPT_URL, $url);
                        curl_setopt($curl, CURLOPT_POST, true);
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $json_string);
                        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        $data = curl_exec($curl);
                        $data = json_decode($data, true);
                        curl_close($curl);
                        if ($data['code'] != 200) {
                            $date = \Illuminate\Support\Facades\Date::now()."\n";
                            $fp = fopen(base_path('storage/logs/pathao_entry_log.txt'), 'a'); // opens file in append mode
                            fwrite($fp, $date.json_encode($data)."\n\n");
                            fclose($fp);
                        }
                        // dd($data['data']->consignment_id);

                        $order_id->update([
                            'status' => $request->status,
                            'pathao_consignment_id' => $data['code'] == 200 ? $data['data']['consignment_id'] : null,
                        ]);
                    }
                } elseif ($request->courier_id == 2) {
                    // redx courier entry
                    $redx_credential = DB::table('redx_apis')->select('is_active', 'access_token')->where('id', 1)->first();
                    if ($redx_credential->is_active == 1) {
                        // get delivery_area
                        $curl = curl_init();

                        curl_setopt_array($curl, [
                            CURLOPT_URL => 'https://openapi.redx.com.bd/v1.0.0-beta/areas',
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'GET',
                            CURLOPT_HTTPHEADER => [
                                'API-ACCESS-TOKEN: Bearer '.$redx_credential->access_token,
                            ],
                        ]);
                        $response = curl_exec($curl);
                        curl_close($curl);
                        $delivery_areas = '';
                        foreach (json_decode($response, true)['areas'] as $delivery_area) {
                            if ($delivery_area['id'] == $order_id->courier_city_id) {
                                $delivery_areas = $delivery_area['name'];
                                break;
                            }
                        }

                        // store order into redx
                        $url = 'https://openapi.redx.com.bd/v1.0.0-beta/parcel';
                        $curl = curl_init();
                        $vars = [
                            'customer_name' => $order_id->customer_name ?? null,
                            'customer_phone' => $order_id->customer_phone ?? null,
                            'delivery_area' => $delivery_areas ?? null,
                            'delivery_area_id' => $order_id->courier_city_id ?? null,
                            'customer_address' => $order_id->customer_address ?? null,
                            'merchant_invoice_id' => $order_id->invoice_id ?? null,
                            'cash_collection_amount' => $order_id->due ?? 0,
                            'parcel_weight' => 500,
                            'instruction' => '',
                            'value' => $order_id->due ?? 0,
                        ];
                        $headers = [
                            'API-ACCESS-TOKEN: Bearer '.$redx_credential->access_token,
                            'Content-Type: application/json',
                        ];
                        $json_string = json_encode($vars);
                        // dd($json_string);
                        curl_setopt_array($curl, [
                            CURLOPT_HTTPHEADER => $headers,
                            CURLOPT_URL => $url,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => $json_string,

                        ]);
                        $response = curl_exec($curl);
                        curl_close($curl);

                        $order_id->update([
                            'status' => $request->status,
                            'redx_tracking_id' => json_decode($response, true)['tracking_id'] ?? null,
                        ]);
                    }
                }
            } else {
                $order_id->update([
                    'status' => $request->status,
                ]);
            }

            return true;
        } else {
            return false;
        }
    }

    public function status(Request $request)
    {
        $access_token = DB::table('web_settings')->select('api_access_token')->where('id', 1)->first()->api_access_token;
        abort_if($access_token != $request->bearerToken(), 403, 'Unauthorized Access');

        $status = $request->input('status');
        $id = $request->input('id');

        $web_settings = DB::table('web_settings')->where('id', 1)->first();
        $order_id = Order::with('get_products.get_product')->find($id);

        if ($order_id->status != 5 && $status == 5) {
            // send order confirm sms
            if ($web_settings->is_order_confirm_sms == 1) {
                $mgs_body = $web_settings->order_confirm_sms;
                $products = '';
                foreach ($order_id->get_products as $key => $item) {
                    if ($key != 0) {
                        $products .= "\n";
                    }
                    $products .= $item->get_product->name.'.';
                }
                $msg = $mgs_body."\n\nOrder No. - ".$order_id->invoice_id."\nProduct(s) - ".$products."\nTotal Price - TK".$order_id->total.' (Inc. Delivery Charge)'.config('default_text.sms_footer');
                // $text = str_replace(' ', '+', $msg);
                // $text = urlencode($msg);
                $text = $msg;

                $apikey = config('app.sms_api_key');
                // $sender = config('app.sms_sender');

                $msisdn = ltrim((string) BanglaToEnglishConverter::bn2en($order_id->customer_phone), '+');
                // dd($apikey, $msisdn, $text);
                $curl = curl_init();

                curl_setopt_array($curl, [
                    CURLOPT_URL => 'https://api.sms.net.bd/sendsms',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => ['api_key' => $apikey, 'msg' => $text, 'to' => $msisdn],
                ]);

                $response = curl_exec($curl);

                curl_close($curl);
                // dd($response);
            }

            if ($order_id->courier_id == 1) {
                // pathao courier entry
                $credential = DB::table('pathao_apis')->select('is_active', 'access_token', 'store_id')->where('id', 1)->first();
                if ($credential->is_active == 1) {
                    $url = 'https://api-hermes.pathao.com/aladdin/api/v1/orders';
                    $item_description = '';
                    foreach ($order_id->get_products as $get_product) {
                        $item_description .= $get_product->get_product->name."\n";
                    }
                    $curl = curl_init();
                    $vars = [
                        'store_id' => $credential->store_id,
                        'merchant_order_id' => $order_id->invoice_id ?? null,
                        'sender_name' => env('APP_NAME'),
                        // 'sender_phone' => null,
                        'recipient_name' => $order_id->customer_name ?? null,
                        'recipient_phone' => $order_id->customer_phone ?? null,
                        'recipient_address' => $order_id->customer_address ?? null,
                        'recipient_city' => $order_id->courier_city_id ?? null,
                        'recipient_zone' => $order_id->courier_zone_id ?? null,
                        'recipient_area' => null,
                        'delivery_type' => 48,
                        'item_type' => 2,
                        'special_instruction' => null,
                        'item_quantity' => $order_id->get_products->sum('qty') ?? 1,
                        'item_weight' => 0.5,
                        'amount_to_collect' => $order_id->due ?? 0,
                        'item_description' => $item_description ?? null,
                    ];
                    $headers = [
                        'accept: application/json',
                        'content-type: application/json',
                        'authorization: Bearer '.$credential->access_token,
                    ];
                    // dd($vars);
                    $json_string = json_encode($vars);
                    // dd($json_string);
                    curl_setopt($curl, CURLOPT_URL, $url);
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $json_string);
                    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    $data = curl_exec($curl);
                    $data = json_decode($data, true);
                    curl_close($curl);

                    if ($data['code'] != 200) {
                        $date = \Illuminate\Support\Facades\Date::now()."\n";
                        $fp = fopen(base_path('storage/logs/pathao_entry_log.txt'), 'a'); // opens file in append mode
                        fwrite($fp, $date.json_encode($data)."\n\n");
                        fclose($fp);
                    }
                    // dd($data['data']->consignment_id);

                    $order_id->update([
                        'status' => $status,
                        'pathao_consignment_id' => $data['code'] == 200 ? $data['data']['consignment_id'] : null,
                    ]);
                } else {
                    $order_id->update([
                        'status' => $status,
                    ]);
                }
            } elseif ($order_id->courier_id == 2) {
                // redx courier entry
                $redx_credential = DB::table('redx_apis')->select('is_active', 'access_token')->where('id', 1)->first();
                if ($redx_credential->is_active == 1) {
                    // get delivery_area
                    $curl = curl_init();

                    curl_setopt_array($curl, [
                        CURLOPT_URL => 'https://openapi.redx.com.bd/v1.0.0-beta/areas',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                        CURLOPT_HTTPHEADER => [
                            'API-ACCESS-TOKEN: Bearer '.$redx_credential->access_token,
                        ],
                    ]);
                    $response = curl_exec($curl);
                    curl_close($curl);
                    $delivery_areas = '';
                    foreach (json_decode($response, true)['areas'] as $delivery_area) {
                        if ($delivery_area['id'] == $order_id->courier_city_id) {
                            $delivery_areas = $delivery_area['name'];
                            break;
                        }
                    }

                    // store order into redx
                    $url = 'https://openapi.redx.com.bd/v1.0.0-beta/parcel';
                    $curl = curl_init();
                    $vars = [
                        'customer_name' => $order_id->customer_name ?? null,
                        'customer_phone' => $order_id->customer_phone ?? null,
                        'delivery_area' => $delivery_areas ?? null,
                        'delivery_area_id' => $order_id->courier_city_id ?? null,
                        'customer_address' => $order_id->customer_address ?? null,
                        'merchant_invoice_id' => $order_id->invoice_id ?? null,
                        'cash_collection_amount' => $order_id->due ?? 0,
                        'parcel_weight' => 500,
                        'instruction' => '',
                        'value' => $order_id->due ?? 0,
                    ];
                    $headers = [
                        'API-ACCESS-TOKEN: Bearer '.$redx_credential->access_token,
                        'Content-Type: application/json',
                    ];
                    $json_string = json_encode($vars);
                    // dd($json_string);
                    curl_setopt_array($curl, [
                        CURLOPT_HTTPHEADER => $headers,
                        CURLOPT_URL => $url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => $json_string,

                    ]);
                    $response = curl_exec($curl);
                    curl_close($curl);

                    $order_id->update([
                        'status' => $status,
                        'redx_tracking_id' => json_decode($response, true)['tracking_id'] ?? null,
                    ]);
                } else {
                    $order_id->update([
                        'status' => $status,
                    ]);
                }
            } else {
                $order_id->update([
                    'status' => $status,
                ]);
            }
        } else {
            $order_id->update([
                'status' => $status,
            ]);
        }

        // create transaction
        $status_name = '';
        if ($status == 0) {
            $status_name = 'On Hold';
        } elseif ($status == 1) {
            $status_name = 'Delivered';
        } elseif ($status == 2) {
            $status_name = 'Processing';
        } elseif ($status == 3) {
            $status_name = 'Pending Payment';
        } elseif ($status == 4) {
            $status_name = 'Cancelled';
        } elseif ($status == 5) {
            $status_name = 'Pending Delivery';
        } elseif ($status == 6) {
            $status_name = 'On Delivery';
        } elseif ($status == 7) {
            $status_name = 'Returned';
        } elseif ($status == 7) {
            $status_name = 'Returned';
        } elseif ($status == 8) {
            $status_name = 'Courier Hold';
        } elseif ($status == 9) {
            $status_name = 'No Response 1';
        } elseif ($status == 10) {
            $status_name = 'No Response 2';
        }

        // create transaction
        if ($request->input('role_id') == 1 || $request->input('role_id') == 2) {// super admin and admin
            $user_name = urldecode((string) $request->input('user_name'));
            $user_id = $request->input('user_id');
            $created_by = 'admin';
        } elseif ($request->input('role_id') == 3) {// manager
            $user_name = urldecode((string) $request->input('user_name'));
            $user_id = $request->input('user_id');
            $created_by = 'manager';
        } elseif ($request->input('role_id') == 4) {// employee
            $user_name = urldecode((string) $request->input('user_name'));
            $user_id = $request->input('user_id');
            $created_by = 'employee';
        }

        order_transaction(
            'api',
            $id,
            strtr(config('transaction_texts.order_status_change'), [
                '{status}' => $status_name,
                '{user_name}' => $user_name,
                '{role}' => $created_by,
            ]),
            null,
            $created_by,
            $user_id,
            null
        );

        return back()->with('success', 'Order Status Changed Successfully');
    }

    public function bulkStatus(Request $request)
    {
        $access_token = DB::table('web_settings')->select('api_access_token')->where('id', 1)->first()->api_access_token;
        abort_if($access_token != $request->bearerToken(), 403, 'Unauthorized Access');

        // return $request->all();
        // dd(explode(',',$request->all_status));
        foreach (explode(',', (string) $request->input('all_ids')) as $item) {
            $web_settings = DB::table('web_settings')->where('id', 1)->first();
            $order_id = Order::with('get_products')->find($item);

            if ($order_id->status != 5 && $request->input('status') == 5) {
                // send order confirm sms
                if ($web_settings->is_order_confirm_sms == 1) {
                    $mgs_body = $web_settings->order_confirm_sms;
                    $products = '';
                    foreach ($order_id->get_products as $key => $item2) {
                        if ($key != 0) {
                            $products .= "\n";
                        }
                        $products .= $item2->get_product->name;
                    }
                    $msg = $mgs_body."\n\nOrder No. - ".$order_id->invoice_id."\nProduct(s) - ".$products."\nTotal Price - TK".$order_id->total.' (Inc. Delivery Charge)'.config('default_text.sms_footer');
                    // $text = str_replace(' ', '+', $msg);
                    // $text = urlencode($msg);
                    $text = $msg;

                    $apikey = config('app.sms_api_key');
                    // $sender = config('app.sms_sender');

                    $msisdn = ltrim((string) BanglaToEnglishConverter::bn2en($order_id->customer_phone), '+');
                    // dd($apikey, $msisdn, $text);
                    $curl = curl_init();

                    curl_setopt_array($curl, [
                        CURLOPT_URL => 'https://api.sms.net.bd/sendsms',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => ['api_key' => $apikey, 'msg' => $text, 'to' => $msisdn],
                    ]);

                    $response = curl_exec($curl);

                    curl_close($curl);
                    // dd($response);
                }

                if ($order_id->courier_id == 1) {
                    // pathao courier entry
                    $credential = DB::table('pathao_apis')->select('is_active', 'access_token', 'store_id')->where('id', 1)->first();
                    if ($credential->is_active == 1) {
                        $url = 'https://api-hermes.pathao.com/aladdin/api/v1/orders';
                        $item_description = '';
                        foreach ($order_id->get_products as $get_product) {
                            $item_description .= $get_product->get_product->name."\n";
                        }
                        $curl = curl_init();
                        $vars = [
                            'store_id' => $credential->store_id,
                            'merchant_order_id' => $order_id->invoice_id ?? null,
                            'sender_name' => env('APP_NAME'),
                            // 'sender_phone' => null,
                            'recipient_name' => $order_id->customer_name ?? null,
                            'recipient_phone' => $order_id->customer_phone ?? null,
                            'recipient_address' => $order_id->customer_address ?? null,
                            'recipient_city' => $order_id->courier_city_id ?? null,
                            'recipient_zone' => $order_id->courier_zone_id ?? null,
                            'recipient_area' => null,
                            'delivery_type' => 48,
                            'item_type' => 2,
                            'special_instruction' => null,
                            'item_quantity' => $order_id->get_products->sum('qty') ?? 1,
                            'item_weight' => 0.5,
                            'amount_to_collect' => $order_id->due ?? 0,
                            'item_description' => $item_description ?? null,
                        ];
                        $headers = [
                            'accept: application/json',
                            'content-type: application/json',
                            'authorization: Bearer '.$credential->access_token,
                        ];
                        // dd($vars);
                        $json_string = json_encode($vars);
                        // dd($json_string);
                        curl_setopt($curl, CURLOPT_URL, $url);
                        curl_setopt($curl, CURLOPT_POST, true);
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $json_string);
                        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        $data = curl_exec($curl);
                        $data = json_decode($data, true);
                        curl_close($curl);
                        if ($data['code'] != 200) {
                            $date = \Illuminate\Support\Facades\Date::now()."\n";
                            $fp = fopen(base_path('storage/logs/pathao_entry_log.txt'), 'a'); // opens file in append mode
                            fwrite($fp, $date.json_encode($data)."\n\n");
                            fclose($fp);
                        }
                        // dd($data['data']->consignment_id);

                        $order_id->update([
                            'status' => $request->input('status'),
                            'pathao_consignment_id' => $data['code'] == 200 ? $data['data']['consignment_id'] : null,
                        ]);
                    } else {
                        $order_id->update([
                            'status' => $request->input('status'),
                        ]);
                    }
                } elseif ($order_id->courier_id == 2) {
                    // redx courier entry
                    $redx_credential = DB::table('redx_apis')->select('is_active', 'access_token')->where('id', 1)->first();
                    if ($redx_credential->is_active == 1) {
                        // get delivery_area
                        $curl = curl_init();

                        curl_setopt_array($curl, [
                            CURLOPT_URL => 'https://openapi.redx.com.bd/v1.0.0-beta/areas',
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'GET',
                            CURLOPT_HTTPHEADER => [
                                'API-ACCESS-TOKEN: Bearer '.$redx_credential->access_token,
                            ],
                        ]);
                        $response = curl_exec($curl);
                        curl_close($curl);
                        $delivery_areas = '';
                        foreach (json_decode($response, true)['areas'] as $delivery_area) {
                            if ($delivery_area['id'] == $order_id->courier_city_id) {
                                $delivery_areas = $delivery_area['name'];
                                break;
                            }
                        }

                        // store order into redx
                        $url = 'https://openapi.redx.com.bd/v1.0.0-beta/parcel';
                        $curl = curl_init();
                        $vars = [
                            'customer_name' => $order_id->customer_name ?? null,
                            'customer_phone' => $order_id->customer_phone ?? null,
                            'delivery_area' => $delivery_areas ?? null,
                            'delivery_area_id' => $order_id->courier_city_id ?? null,
                            'customer_address' => $order_id->customer_address ?? null,
                            'merchant_invoice_id' => $order_id->invoice_id ?? null,
                            'cash_collection_amount' => $order_id->due ?? 0,
                            'parcel_weight' => 500,
                            'instruction' => '',
                            'value' => $order_id->due ?? 0,
                        ];
                        $headers = [
                            'API-ACCESS-TOKEN: Bearer '.$redx_credential->access_token,
                            'Content-Type: application/json',
                        ];
                        $json_string = json_encode($vars);
                        // dd($json_string);
                        curl_setopt_array($curl, [
                            CURLOPT_HTTPHEADER => $headers,
                            CURLOPT_URL => $url,
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => $json_string,

                        ]);
                        $response = curl_exec($curl);
                        curl_close($curl);

                        $order_id->update([
                            'status' => $request->input('status'),
                            'redx_tracking_id' => json_decode($response, true)['tracking_id'] ?? null,
                        ]);
                    } else {
                        $order_id->update([
                            'status' => $request->input('status'),
                        ]);
                    }
                } else {
                    $order_id->update([
                        'status' => $request->input('status'),
                    ]);
                }
            } else {
                $order_id->update([
                    'status' => $request->input('status'),
                ]);
            }

            // create transaction
            $status_name = '';
            if ($request->status == 0) {
                $status_name = 'On Hold';
            } elseif ($request->status == 1) {
                $status_name = 'Delivered';
            } elseif ($request->status == 2) {
                $status_name = 'Processing';
            } elseif ($request->status == 3) {
                $status_name = 'Pending Payment';
            } elseif ($request->status == 4) {
                $status_name = 'Cancelled';
            } elseif ($request->status == 5) {
                $status_name = 'Pending Delivery';
            } elseif ($request->status == 6) {
                $status_name = 'On Delivery';
            } elseif ($request->status == 7) {
                $status_name = 'Returned';
            } elseif ($request->status == 8) {
                $status_name = 'Courier Hold';
            } elseif ($request->status == 9) {
                $status_name = 'No Response 1';
            } elseif ($request->status == 10) {
                $status_name = 'No Response 2';
            }

            // create transaction
            if ($request->input('role_id') == 1 || $request->input('role_id') == 2) {// super admin and admin
                $user_name = urldecode((string) $request->input('user_name'));
                $user_id = $request->input('user_id');
                $created_by = 'admin';
            } elseif ($request->input('role_id') == 3) {// manager
                $user_name = urldecode((string) $request->input('user_name'));
                $user_id = $request->input('user_id');
                $created_by = 'manager';
            } elseif ($request->input('role_id') == 4) {// employee
                $user_name = urldecode((string) $request->input('user_name'));
                $user_id = $request->input('user_id');
                $created_by = 'employee';
            }

            order_transaction(
                'api',
                $item,
                strtr(config('transaction_texts.order_status_change'), [
                    '{status}' => $status_name,
                    '{user_name}' => $user_name,
                    '{role}' => $created_by,
                ]),
                null,
                $created_by,
                $user_id,
                null
            );
        }

        return true;
    }

    public function paymentStatus(Request $request)
    {
        $access_token = DB::table('web_settings')->select('api_access_token')->where('id', 1)->first()->api_access_token;
        abort_if($access_token != $request->bearerToken(), 403, 'Unauthorized Access');

        $id = $request->input('id');
        $status = $request->input('status');
        Order::find($id)->update([
            'payment_status' => $status,
        ]);

        // create transaction
        $status_name = '';
        if ($status == 0) {
            $status_name = 'Unpaid';
        } elseif ($status == 1) {
            $status_name = 'Partial';
        } elseif ($status == 2) {
            $status_name = 'Paid';
        }

        // create transaction
        if ($request->input('role_id') == 1 || $request->input('role_id') == 2) {// super admin and admin
            $user_name = urldecode((string) $request->input('user_name'));
            $user_id = $request->input('user_id');
            $created_by = 'admin';
        } elseif ($request->input('role_id') == 3) {// manager
            $user_name = urldecode((string) $request->input('user_name'));
            $user_id = $request->input('user_id');
            $created_by = 'manager';
        } elseif ($request->input('role_id') == 4) {// employee
            $user_name = urldecode((string) $request->input('user_name'));
            $user_id = $request->input('user_id');
            $created_by = 'employee';
        }

        order_transaction(
            'api',
            $id,
            strtr(config('transaction_texts.order_payment_status_change'), [
                '{status}' => $status_name,
                '{user_name}' => $user_name,
                '{role}' => $created_by,
            ]),
            null,
            $created_by,
            $user_id,
            null
        );

        return 'success';
    }

    public function productCourier(Request $request)
    {
        $access_token = DB::table('web_settings')->select('api_access_token')->where('id', 1)->first()->api_access_token;
        abort_if($access_token != $request->bearerToken(), 403, 'Unauthorized Access');

        $data['products'] = DB::table('products')
            /* ->where('status', 1) */
            ->select('name', 'id')
            ->get();
        $data['couriers'] = DB::table('couriers')->where('status', 1)->pluck('courier_name', 'id');

        return $data;
    }

    public function productInfo(Request $request)
    {
        $access_token = DB::table('web_settings')->select('api_access_token')->where('id', 1)->first()->api_access_token;
        abort_if($access_token != $request->bearerToken(), 403, 'Unauthorized Access');

        $products = Product::with('get_attributes')->where('id', $request->input('id'))->first();

        return $products;
    }

    public function pathaoCities(Request $request)
    {
        $access_token = DB::table('web_settings')->select('api_access_token')->where('id', 1)->first()->api_access_token;
        abort_if($access_token != $request->bearerToken(), 403, 'Unauthorized Access');

        $credential = DB::table('pathao_apis')->select('access_token')->where('id', 1)->first();
        // dd($credential);
        $url = 'https://api-hermes.pathao.com/aladdin/api/v1/countries/1/city-list';
        $curl = curl_init();
        $headers = [
            'accept: application/json',
            'content-type: application/json',
            'Authorization: Bearer '.$credential->access_token,
        ];
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $d = curl_exec($curl);
        $d = json_decode($d, true);
        curl_close($curl);

        $data = [];
        foreach ($d['data']['data'] as $item) {
            $data[$item['city_id']] = $item['city_name'];
        }

        return $data;

    }

    public function pathaoZones(Request $request)
    {
        $access_token = DB::table('web_settings')->select('api_access_token')->where('id', 1)->first()->api_access_token;
        abort_if($access_token != $request->bearerToken(), 403, 'Unauthorized Access');

        $credential = DB::table('pathao_apis')->select('access_token')->where('id', 1)->first();
        // dd($credential);
        $url = 'https://api-hermes.pathao.com/aladdin/api/v1/cities/'.$request->input('id').'/zone-list';
        $curl = curl_init();
        $headers = [
            'accept: application/json',
            'content-type: application/json',
            'Authorization: Bearer '.$credential->access_token,
        ];
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $d = curl_exec($curl);
        $d = json_decode($d, true);
        curl_close($curl);

        $data = [];
        foreach ($d['data']['data'] as $item) {
            $data[$item['zone_id']] = $item['zone_name'];
        }

        return response()->json($data);
    }

    public function carrybeeCities(Request $request)
    {
        $access_token = DB::table('web_settings')->select('api_access_token')->where('id', 1)->first()->api_access_token;
        abort_if($access_token != $request->bearerToken(), 403, 'Unauthorized Access');

        $credential = DB::table('carry_bee_apis')->select('access_token')->where('id', 1)->first();
        // dd($credential);
        $url = 'https://developers.carrybee.com/api/city-list';
        $curl = curl_init();
        $headers = [
            'accept: application/json',
            'content-type: application/json',
            'Authorization: Bearer '.$credential->access_token,
        ];
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $d = curl_exec($curl);
        $d = json_decode($d, true);
        curl_close($curl);

        $data = [];
        foreach ($d['data']['data'] as $item) {
            $data[$item['city_id']] = $item['city_name'];
        }

        return $data;

    }

    public function carrybeeZones(Request $request)
    {
        $access_token = DB::table('web_settings')->select('api_access_token')->where('id', 1)->first()->api_access_token;
        abort_if($access_token != $request->bearerToken(), 403, 'Unauthorized Access');

        $credential = DB::table('carry_bee_apis')->select('access_token')->where('id', 1)->first();
        // dd($credential);
        $url = 'https://developers.carrybee.com/api/cities/'.$request->input('id').'/zones';
        $curl = curl_init();
        $headers = [
            'accept: application/json',
            'content-type: application/json',
            'Authorization: Bearer '.$credential->access_token,
        ];
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $d = curl_exec($curl);
        $d = json_decode($d, true);
        curl_close($curl);

        $data = [];
        foreach ($d['data']['data'] as $item) {
            $data[$item['zone_id']] = $item['zone_name'];
        }

        return response()->json($data);
    }

    public function redxCities(Request $request)
    {
        $access_token = DB::table('web_settings')->select('api_access_token')->where('id', 1)->first()->api_access_token;
        abort_if($access_token != $request->bearerToken(), 403, 'Unauthorized Access');

        $credential = DB::table('redx_apis')->select('is_active', 'access_token')->where('id', 1)->first();
        $url = 'https://openapi.redx.com.bd/v1.0.0-beta/areas';
        $curl = curl_init();
        $headers = [
            'API-ACCESS-TOKEN: Bearer '.$credential->access_token,
        ];

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => $headers,
        ]);

        $d1 = curl_exec($curl);
        $d1 = json_decode($d1, true)['areas'];
        curl_close($curl);

        $data = [];
        foreach ($d1 as $item) {
            $data[$item['id']] = $item['division_name'].' > '.$item['district_name'].' > '.$item['name'];
        }

        return response()->json($data);
    }

    public function delete(Request $request)
    {
        $access_token = DB::table('web_settings')->select('api_access_token')->where('id', 1)->first()->api_access_token;
        abort_if($access_token != $request->bearerToken(), 403, 'Unauthorized Access');

        $order = Order::find($request->id);

        if ($order->status == 1) {
            $data = ['warning', 'Delivered Order Can\'t Be Deleted!'];
        } else {
            $order->delete();
            $data = ['success', 'Order Deleted Successfully'];
        }

        return response()->json($data);
    }

    public function printInvoice(Request $request)
    {
        $access_token = DB::table('web_settings')->select('api_access_token')->where('id', 1)->first()->api_access_token;
        abort_if($access_token != $request->bearerToken(), 403, 'Unauthorized Access');

        $id = explode(',', $request->id);
        $data['order'] = Order::with('get_products.get_product', 'get_courier')->find($id);
        $data['settings'] = WebSettings::with('get_logo')->first();
        $data['settings']->website_header_logo = asset($data['settings']->get_logo->file_url);

        return $data;
    }

    public function bulkLabelPrint(Request $request)
    {
        $access_token = DB::table('web_settings')->select('api_access_token')->where('id', 1)->first()->api_access_token;
        abort_if($access_token != $request->bearerToken(), 403, 'Unauthorized Access');

        $id = explode(',', $request->id);
        $data['order'] = Order::with('get_products.get_product', 'get_courier')->find($id);
        $data['settings'] = WebSettings::with('get_logo')->first();
        $data['settings']->website_header_logo = asset($data['settings']->get_logo->file_url);

        return $data;
    }

    public function courierCsvExport(Request $request)
    {
        $access_token = DB::table('web_settings')->select('api_access_token')->where('id', 1)->first()->api_access_token;
        abort_if($access_token != $request->bearerToken(), 403, 'Unauthorized Access');

        $data = Order::with('get_products.get_product', 'get_courier', 'get_shipping_method')->find(explode(',', $request->id));
        foreach ($data as $i) {
            if ($i->courier_id == 1) { // 1=pathao
                $credential = DB::table('pathao_apis')->select('is_active', 'access_token')->where('id', 1)->first();
                // dd($credential);
                $url = 'https://api-hermes.pathao.com/aladdin/api/v1/countries/1/city-list';
                $curl = curl_init();
                $headers = [
                    'accept: application/json',
                    'content-type: application/json',
                    'Authorization: Bearer '.$credential->access_token,
                ];
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_POST, false);
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $d1 = curl_exec($curl);
                $d1 = json_decode($d1, true);
                // curl_close($curl);

                foreach ($d1['data']['data'] as $item) {
                    if ($i->courier_city_id == $item['city_id']) {
                        $i['courier_city'] = $item['city_name'];
                    }
                }

                // dd($credential);
                $url = 'https://api-hermes.pathao.com/aladdin/api/v1/cities/'.$i->courier_city_id.'/zone-list';
                // $curl = curl_init();

                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_POST, false);
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $d2 = curl_exec($curl);
                $d2 = json_decode($d2, true);
                curl_close($curl);

                foreach ($d2['data']['data'] as $item) {
                    if ($i->courier_zone_id == $item['zone_id']) {
                        $i['courier_zone'] = $item['zone_name'];
                    }
                }
            } else {
                $i['courier_city'] = null;
                $i['courier_zone'] = null;
            }
        }

        return $data;
    }

    public function singleAssign(Request $request)
    {
        $access_token = DB::table('web_settings')->select('api_access_token')->where('id', 1)->first()->api_access_token;
        abort_if($access_token != $request->bearerToken(), 403, 'Unauthorized Access');

        // dd($request->all());
        $emp_id = DB::table('employees')->where('p_id', $request->employee_id)->first();
        /*if ($emp_id) {
            $emp_id = $emp_id->id;
        }*/

        $check = OrderAssign::where('order_id', $request->order_id)->first();
        if ($check) {
            OrderAssign::where('order_id', $request->order_id)->update([
                'employee_id' => $emp_id->id,
            ]);
        } else {
            OrderAssign::create([
                'order_id' => $request->order_id,
                'employee_id' => $emp_id->id,
            ]);
        }

        // create transaction
        if ($request->input('role_id') == 1 || $request->input('role_id') == 2) {// super admin and admin
            $user_name = urldecode((string) $request->input('user_name'));
            $user_id = $request->input('user_id');
            $created_by = 'admin';
        } elseif ($request->input('role_id') == 3) {// manager
            $user_name = urldecode((string) $request->input('user_name'));
            $user_id = $request->input('user_id');
            $created_by = 'manager';
        } elseif ($request->input('role_id') == 4) {// employee
            $user_name = urldecode((string) $request->input('user_name'));
            $user_id = $request->input('user_id');
            $created_by = 'employee';
        }

        order_transaction(
            'api',
            $request->order_id,
            strtr(config('transaction_texts.order_assign'), [
                '{employee_name}' => $emp_id->name,
                '{user_name}' => $user_name,
                '{role}' => $created_by,
            ]),
            null,
            $created_by,
            $user_id,
            $emp_id->id
        );

    }

    public function bulkAssign(Request $request)
    {
        $access_token = DB::table('web_settings')->select('api_access_token')->where('id', 1)->first()->api_access_token;
        abort_if($access_token != $request->bearerToken(), 403, 'Unauthorized Access');

        // dd($request->all());

        $emp_id = DB::table('employees')->where('p_id', $request->employee_id)->first();
        /*if ($emp_id) {
            $emp_id = $emp_id->id;
        }*/

        foreach (explode(',', $request->all_ids) as $item) {
            $check = OrderAssign::where('order_id', $item)->first();
            if ($check) {
                OrderAssign::where('order_id', $item)->update([
                    'employee_id' => $emp_id->id,
                ]);
            } else {
                OrderAssign::create([
                    'order_id' => $item,
                    'employee_id' => $emp_id->id,
                ]);
            }

            // $emp = DB::table('employees')->select('name')->where('id', $request->employee_id)->first();
            // create transaction
            if ($request->input('role_id') == 1 || $request->input('role_id') == 2) {// super admin and admin
                $user_name = urldecode((string) $request->input('user_name'));
                $user_id = $request->input('user_id');
                $created_by = 'admin';
            } elseif ($request->input('role_id') == 3) {// manager
                $user_name = urldecode((string) $request->input('user_name'));
                $user_id = $request->input('user_id');
                $created_by = 'manager';
            } elseif ($request->input('role_id') == 4) {// employee
                $user_name = urldecode((string) $request->input('user_name'));
                $user_id = $request->input('user_id');
                $created_by = 'employee';
            }

            order_transaction(
                'api',
                $item,
                strtr(config('transaction_texts.order_assign'), [
                    '{employee_name}' => $emp_id->name,
                    '{user_name}' => $user_name,
                    '{role}' => $created_by,
                ]),
                null,
                $created_by,
                $user_id,
                $emp_id->id
            );
        }
    }

    public function transactionView(Request $request)
    {
        $access_token = DB::table('web_settings')->select('api_access_token')->where('id', 1)->first()->api_access_token;
        abort_if($access_token != $request->bearerToken(), 403, 'Unauthorized Access');

        $data = DB::table('order_transactions')->select('type', 'text', 'created_at')->where('order_id', $request->id)->orderBy('id', 'desc')->get();

        return response()->json($data);
    }

    public function noteUpdate(Request $request)
    {
        $access_token = DB::table('web_settings')->select('api_access_token')->where('id', 1)->first()->api_access_token;
        abort_if($access_token != $request->bearerToken(), 403, 'Unauthorized Access');

        Order::find($request->id)->update($request->all());

        return true;
    }

    public function sendSms(Request $request)
    {
        $access_token = DB::table('web_settings')->select('api_access_token')->where('id', 1)->first()->api_access_token;
        abort_if($access_token != $request->bearerToken(), 403, 'Unauthorized Access');

        // dd($request->all());
        $apikey = config('app.sms_api_key');
        // $sender = config('app.sms_sender');

        $msisdn = ltrim((string) BanglaToEnglishConverter::bn2en($request->customer_phone), '+');
        // dd($apikey, $msisdn, $text);
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.sms.net.bd/sendsms',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => ['api_key' => $apikey, 'msg' => $request->sms_body, 'to' => $msisdn],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);
        // dd($response);
        if (json_decode($response, true)['error'] == 0) {
            return response()->json(['success' => 'SMS Sent Successfully']);
        } else {
            return response()->json(['error' => json_decode($response, true)['msg']]);
        }
    }
}
