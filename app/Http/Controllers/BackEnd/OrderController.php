<?php

namespace App\Http\Controllers\BackEnd;

use App\Exports\OrderExport;
use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeItem;
use App\Models\Courier;
use App\Models\CourierCity;
use App\Models\CourierZone;
use App\Models\Employee;
use App\Models\NoteHistory;
use App\Models\Order;
use App\Models\OrderAssign;
use App\Models\OrderProduct;
use App\Models\OrderTransaction;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Models\SmsSetting;
use App\Models\User;
use App\Models\WebSettings;
use App\Services\BanglaToEnglishConverter;
use App\Services\WhatsappServices;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{
    protected $WpServices;

    // Dependency Injection via constructor
    public function __construct(WhatsappServices $WpServices)
    {
        $this->WpServices = $WpServices;
    }

    public function index(Request $request)
    {
        // dd($this->WpServices);
        if ($request->input('paginate') != null) {
            $paginate = $request->input('paginate');
        } else {
            $paginate = 10;
        }

        //        dd($request->all());
        $courier_id = $request->input('courier_id') ?? null;
        $query = $request->input('query') ?? null;
        $status = $request->input('status');
        $courier_status = $request->input('courier_status');

        $custom_range = $request->input('custom_range');

        if ($status == 'Hold') {
            // dd('here');
            $sts = 0;
        } elseif ($status == 'Delivered') {
            $sts = 1;
        } elseif ($status == 'Processing') {
            $sts = 2;
        } elseif ($status == 'Pending Payment') {
            $sts = 3;
        } elseif ($status == 'Cancelled') {
            $sts = 4;
        } elseif ($status == 'Pending Invoice') {
            $sts = 5;
        } elseif ($status == 'On Delivery') {
            $sts = 6;
        } elseif ($status == 'Pending Return') {
            $sts = 7;
        } elseif ($status == 'Courier') {
            $sts = 8;
        } elseif ($status == 'No Response') {
            $sts = 9;
        } elseif ($status == 'Invoiced') {
            $sts = 10;
        } elseif ($status == 'Return') {
            $sts = 11;
        } elseif ($status == 'Incomplete') {
            $sts = 12;
        } elseif ($status == 'Confirmed') {
            $sts = 13;
        } elseif ($status == 'Stock Out') {
            $sts = 14;
        } elseif ($status == 'Partial Delivery') {
            $sts = 15;
        } elseif ($status == 'Lost') {
            $sts = 16;
        } elseif ($status == null) {
            $sts = null;
        } else {
            $sts = 'All';
        }

        if (Auth::guard('admin')->check() || Auth::guard('manager')->check()) {
            $data['couriers'] = DB::table('couriers')->where('status', 1)->pluck('courier_name', 'id');
            $data['shippings'] = DB::table('shipping_methods')->where('status', 1)->pluck('type', 'id');
            $data['employees'] = DB::table('employees')->where('status', 1)->pluck('name', 'id');

            $data['total_order'] = Order::where('deleted_at', null);
            $data['total_order_amount'] = Order::where('deleted_at', null);

            $data['total_hold_order'] = Order::where('deleted_at', null)->where('status', 0);
            $data['total_hold_amount'] = Order::where('deleted_at', null)->where('status', 0);

            $data['total_deliver_order'] = Order::where('deleted_at', null)->where('status', 1);
            $data['total_deliver_amount'] = Order::where('deleted_at', null)->where('status', 1);

            $data['total_process_order'] = Order::where('deleted_at', null)->where('status', 2);
            $data['total_process_amount'] = Order::where('deleted_at', null)->where('status', 2);

            $data['total_pend_pay_order'] = Order::where('deleted_at', null)->where('status', 3);
            $data['total_pend_pay_amount'] = Order::where('deleted_at', null)->where('status', 3);

            $data['total_cancel_order'] = Order::where('deleted_at', null)->where('status', 4);
            $data['total_cancel_amount'] = Order::where('deleted_at', null)->where('status', 4);

            $data['total_pending_invoice_order'] = Order::where('deleted_at', null)->where('status', 5);
            $data['total_pending_invoice_amount'] = Order::where('deleted_at', null)->where('status', 5);

            $data['total_on_delivery_order'] = Order::where('deleted_at', null)->where('status', 6);
            $data['total_on_delivery_amount'] = Order::where('deleted_at', null)->where('status', 6);

            $data['total_pending_return_order'] = Order::where('deleted_at', null)->where('status', 7);
            $data['total_pending_return_amount'] = Order::where('deleted_at', null)->where('status', 7);

            $data['total_courier_hold_order'] = Order::where('deleted_at', null)->where('status', 8);
            $data['total_courier_hold_amount'] = Order::where('deleted_at', null)->where('status', 8);

            $data['total_nr_1_order'] = Order::where('deleted_at', null)->where('status', 9);
            $data['total_nr_1_amount'] = Order::where('deleted_at', null)->where('status', 9);

            $data['total_invoiced_order'] = Order::where('deleted_at', null)->where('status', 10);
            $data['total_invoiced_amount'] = Order::where('deleted_at', null)->where('status', 10);

            $data['total_return_order'] = Order::where('deleted_at', null)->where('status', 11);
            $data['total_return_amount'] = Order::where('deleted_at', null)->where('status', 11);

            $data['total_incomplete_order'] = Order::where('deleted_at', null)->where('status', 12);
            $data['total_incomplete_amount'] = Order::where('deleted_at', null)->where('status', 12);

            $data['total_confirmed_order'] = Order::where('deleted_at', null)->where('status', 13);
            $data['total_confirmed_amount'] = Order::where('deleted_at', null)->where('status', 13);

            $data['total_stock_out_order'] = Order::where('deleted_at', null)->where('status', 14);
            $data['total_stock_out_amount'] = Order::where('deleted_at', null)->where('status', 14);

            $data['total_partial_delivery_order'] = Order::where('deleted_at', null)->where('status', 15);
            $data['total_partial_delivery_amount'] = Order::where('deleted_at', null)->where('status', 15);

            $data['total_lost_order'] = Order::where('deleted_at', null)->where('status', 16);
            $data['total_lost_amount'] = Order::where('deleted_at', null)->where('status', 16);

            $data['orders'] = Order::query();

            if ($custom_range == 'today') {
                $custom_range = \Illuminate\Support\Facades\Date::today()->toDateTimeString();

                $data['total_order']->whereDate('created_at', $custom_range);
                $data['total_order_amount']->whereDate('created_at', $custom_range);

                $data['total_hold_order']->whereDate('created_at', $custom_range);
                $data['total_hold_amount']->whereDate('created_at', $custom_range);

                $data['total_deliver_order']->whereDate('created_at', $custom_range);
                $data['total_deliver_amount']->whereDate('created_at', $custom_range);

                $data['total_process_order']->whereDate('created_at', $custom_range);
                $data['total_process_amount']->whereDate('created_at', $custom_range);

                $data['total_pend_pay_order']->whereDate('created_at', $custom_range);
                $data['total_pend_pay_amount']->whereDate('created_at', $custom_range);

                $data['total_cancel_order']->whereDate('created_at', $custom_range);
                $data['total_cancel_amount']->whereDate('created_at', $custom_range);

                $data['total_pending_invoice_order']->whereDate('created_at', $custom_range);
                $data['total_pending_invoice_amount']->whereDate('created_at', $custom_range);

                $data['total_on_delivery_order']->whereDate('created_at', $custom_range);
                $data['total_on_delivery_amount']->whereDate('created_at', $custom_range);

                $data['total_pending_return_order']->whereDate('created_at', $custom_range);
                $data['total_pending_return_amount']->whereDate('created_at', $custom_range);

                $data['total_courier_hold_order']->whereDate('created_at', $custom_range);
                $data['total_courier_hold_amount']->whereDate('created_at', $custom_range);

                $data['total_nr_1_order']->whereDate('created_at', $custom_range);
                $data['total_nr_1_amount']->whereDate('created_at', $custom_range);

                $data['total_invoiced_order']->whereDate('created_at', $custom_range);
                $data['total_invoiced_amount']->whereDate('created_at', $custom_range);

                $data['total_return_order']->whereDate('created_at', $custom_range);
                $data['total_return_amount']->whereDate('created_at', $custom_range);

                $data['total_incomplete_order']->whereDate('created_at', $custom_range);
                $data['total_incomplete_amount']->whereDate('created_at', $custom_range);

                $data['total_confirmed_order']->whereDate('created_at', $custom_range);
                $data['total_confirmed_amount']->whereDate('created_at', $custom_range);

                $data['total_stock_out_order']->whereDate('created_at', $custom_range);
                $data['total_stock_out_amount']->whereDate('created_at', $custom_range);

                $data['total_partial_delivery_order']->whereDate('created_at', $custom_range);
                $data['total_partial_delivery_amount']->whereDate('created_at', $custom_range);

                $data['total_lost_order']->whereDate('created_at', $custom_range);
                $data['total_lost_amount']->whereDate('created_at', $custom_range);

                $data['orders']->whereDate('created_at', $custom_range);
                // dd($data);
            } elseif ($custom_range == 'yesterday') {
                $custom_range = \Illuminate\Support\Facades\Date::yesterday()->toDateTimeString();

                $data['total_order']->whereDate('created_at', $custom_range);
                $data['total_order_amount']->whereDate('created_at', $custom_range);

                $data['total_hold_order']->whereDate('created_at', $custom_range);
                $data['total_hold_amount']->whereDate('created_at', $custom_range);

                $data['total_deliver_order']->whereDate('created_at', $custom_range);
                $data['total_deliver_amount']->whereDate('created_at', $custom_range);

                $data['total_process_order']->whereDate('created_at', $custom_range);
                $data['total_process_amount']->whereDate('created_at', $custom_range);

                $data['total_pend_pay_order']->whereDate('created_at', $custom_range);
                $data['total_pend_pay_amount']->whereDate('created_at', $custom_range);

                $data['total_cancel_order']->whereDate('created_at', $custom_range);
                $data['total_cancel_amount']->whereDate('created_at', $custom_range);

                $data['total_pending_invoice_order']->whereDate('created_at', $custom_range);
                $data['total_pending_invoice_amount']->whereDate('created_at', $custom_range);

                $data['total_on_delivery_order']->whereDate('created_at', $custom_range);
                $data['total_on_delivery_amount']->whereDate('created_at', $custom_range);

                $data['total_pending_return_order']->whereDate('created_at', $custom_range);
                $data['total_pending_return_amount']->whereDate('created_at', $custom_range);

                $data['total_courier_hold_order']->whereDate('created_at', $custom_range);
                $data['total_courier_hold_amount']->whereDate('created_at', $custom_range);

                $data['total_nr_1_order']->whereDate('created_at', $custom_range);
                $data['total_nr_1_amount']->whereDate('created_at', $custom_range);

                $data['total_invoiced_order']->whereDate('created_at', $custom_range);
                $data['total_invoiced_amount']->whereDate('created_at', $custom_range);

                $data['total_return_order']->whereDate('created_at', $custom_range);
                $data['total_return_amount']->whereDate('created_at', $custom_range);

                $data['total_incomplete_order']->whereDate('created_at', $custom_range);
                $data['total_incomplete_amount']->whereDate('created_at', $custom_range);

                $data['total_confirmed_order']->whereDate('created_at', $custom_range);
                $data['total_confirmed_amount']->whereDate('created_at', $custom_range);

                $data['total_stock_out_order']->whereDate('created_at', $custom_range);
                $data['total_stock_out_amount']->whereDate('created_at', $custom_range);

                $data['total_partial_delivery_order']->whereDate('created_at', $custom_range);
                $data['total_partial_delivery_amount']->whereDate('created_at', $custom_range);

                $data['total_lost_order']->whereDate('created_at', $custom_range);
                $data['total_lost_amount']->whereDate('created_at', $custom_range);

                $data['orders']->whereDate('created_at', $custom_range);
                // dd($data);
            } elseif ($custom_range == 'last_7_days') {
                $custom_range = \Illuminate\Support\Facades\Date::now()->subDays(7)->toDateTimeString();

                $data['total_order']->where('created_at', '>=', $custom_range);
                $data['total_order_amount']->where('created_at', '>=', $custom_range);

                $data['total_hold_order']->where('created_at', '>=', $custom_range);
                $data['total_hold_amount']->where('created_at', '>=', $custom_range);

                $data['total_deliver_order']->where('created_at', '>=', $custom_range);
                $data['total_deliver_amount']->where('created_at', '>=', $custom_range);

                $data['total_process_order']->where('created_at', '>=', $custom_range);
                $data['total_process_amount']->where('created_at', '>=', $custom_range);

                $data['total_pend_pay_order']->where('created_at', '>=', $custom_range);
                $data['total_pend_pay_amount']->where('created_at', '>=', $custom_range);

                $data['total_cancel_order']->where('created_at', '>=', $custom_range);
                $data['total_cancel_amount']->where('created_at', '>=', $custom_range);

                $data['total_pending_invoice_order']->where('created_at', '>=', $custom_range);
                $data['total_pending_invoice_amount']->where('created_at', '>=', $custom_range);

                $data['total_on_delivery_order']->where('created_at', '>=', $custom_range);
                $data['total_on_delivery_amount']->where('created_at', '>=', $custom_range);

                $data['total_pending_return_order']->where('created_at', '>=', $custom_range);
                $data['total_pending_return_amount']->where('created_at', '>=', $custom_range);

                $data['total_courier_hold_order']->where('created_at', '>=', $custom_range);
                $data['total_courier_hold_amount']->where('created_at', '>=', $custom_range);

                $data['total_nr_1_order']->where('created_at', '>=', $custom_range);
                $data['total_nr_1_amount']->where('created_at', '>=', $custom_range);

                $data['total_invoiced_order']->where('created_at', '>=', $custom_range);
                $data['total_invoiced_amount']->where('created_at', '>=', $custom_range);

                $data['total_return_order']->where('created_at', '>=', $custom_range);
                $data['total_return_amount']->where('created_at', '>=', $custom_range);

                $data['total_incomplete_order']->where('created_at', '>=', $custom_range);
                $data['total_incomplete_amount']->where('created_at', '>=', $custom_range);

                $data['total_confirmed_order']->where('created_at', '>=', $custom_range);
                $data['total_confirmed_amount']->where('created_at', '>=', $custom_range);

                $data['total_stock_out_order']->where('created_at', '>=', $custom_range);
                $data['total_stock_out_amount']->where('created_at', '>=', $custom_range);

                $data['total_partial_delivery_order']->where('created_at', '>=', $custom_range);
                $data['total_partial_delivery_amount']->where('created_at', '>=', $custom_range);

                $data['total_lost_order']->where('created_at', '>=', $custom_range);
                $data['total_lost_amount']->where('created_at', '>=', $custom_range);

                $data['orders']->where('created_at', '>=', $custom_range);
            } elseif ($custom_range == 'this_month') {
                $custom_range = \Illuminate\Support\Facades\Date::now()->month;

                $data['total_order']->whereMonth('created_at', $custom_range);
                $data['total_order_amount']->whereMonth('created_at', $custom_range);

                $data['total_hold_order']->whereMonth('created_at', $custom_range);
                $data['total_hold_amount']->whereMonth('created_at', $custom_range);

                $data['total_deliver_order']->whereMonth('created_at', $custom_range);
                $data['total_deliver_amount']->whereMonth('created_at', $custom_range);

                $data['total_process_order']->whereMonth('created_at', $custom_range);
                $data['total_process_amount']->whereMonth('created_at', $custom_range);

                $data['total_pend_pay_order']->whereMonth('created_at', $custom_range);
                $data['total_pend_pay_amount']->whereMonth('created_at', $custom_range);

                $data['total_cancel_order']->whereMonth('created_at', $custom_range);
                $data['total_cancel_amount']->whereMonth('created_at', $custom_range);

                $data['total_pending_invoice_order']->whereMonth('created_at', $custom_range);
                $data['total_pending_invoice_amount']->whereMonth('created_at', $custom_range);

                $data['total_on_delivery_order']->whereMonth('created_at', $custom_range);
                $data['total_on_delivery_amount']->whereMonth('created_at', $custom_range);

                $data['total_pending_return_order']->whereMonth('created_at', $custom_range);
                $data['total_pending_return_amount']->whereMonth('created_at', $custom_range);

                $data['total_courier_hold_order']->whereMonth('created_at', $custom_range);
                $data['total_courier_hold_amount']->whereMonth('created_at', $custom_range);

                $data['total_nr_1_order']->whereMonth('created_at', $custom_range);
                $data['total_nr_1_amount']->whereMonth('created_at', $custom_range);

                $data['total_invoiced_order']->whereMonth('created_at', $custom_range);
                $data['total_invoiced_amount']->whereMonth('created_at', $custom_range);

                $data['total_return_order']->whereMonth('created_at', $custom_range);
                $data['total_return_amount']->whereMonth('created_at', $custom_range);

                $data['total_incomplete_order']->whereMonth('created_at', $custom_range);
                $data['total_incomplete_amount']->whereMonth('created_at', $custom_range);

                $data['total_confirmed_order']->whereMonth('created_at', $custom_range);
                $data['total_confirmed_amount']->whereMonth('created_at', $custom_range);

                $data['total_stock_out_order']->whereMonth('created_at', $custom_range);
                $data['total_stock_out_amount']->whereMonth('created_at', $custom_range);

                $data['total_partial_delivery_order']->whereMonth('created_at', $custom_range);
                $data['total_partial_delivery_amount']->whereMonth('created_at', $custom_range);

                $data['total_lost_order']->whereMonth('created_at', $custom_range);
                $data['total_lost_amount']->whereMonth('created_at', $custom_range);

                $data['orders']->whereMonth('created_at', $custom_range);
            } elseif ($custom_range == 'last_month') {
                $sd = \Illuminate\Support\Facades\Date::now()->subMonth()->startOfMonth()->toDateTimeString();
                $ed = \Illuminate\Support\Facades\Date::now()->subMonth()->endOfMonth()->toDateTimeString();

                $data['total_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_order_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_hold_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_hold_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_deliver_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_deliver_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_process_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_process_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_pend_pay_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_pend_pay_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_cancel_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_cancel_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_pending_invoice_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_pending_invoice_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_on_delivery_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_on_delivery_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_pending_return_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_pending_return_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_courier_hold_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_courier_hold_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_nr_1_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_nr_1_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_invoiced_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_invoiced_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_return_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_return_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_incomplete_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_incomplete_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_confirmed_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_confirmed_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_stock_out_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_stock_out_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_partial_delivery_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_partial_delivery_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_lost_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_lost_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['orders']->whereBetween('created_at', [$sd, $ed]);
            } elseif ($custom_range == 'last_6_months') {
                $sd = \Illuminate\Support\Facades\Date::now()->subMonths(6)->startOfMonth()->toDateTimeString();
                $ed = \Illuminate\Support\Facades\Date::now()->toDateTimeString();

                $data['total_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_order_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_hold_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_hold_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_deliver_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_deliver_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_process_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_process_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_pend_pay_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_pend_pay_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_cancel_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_cancel_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_pending_invoice_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_pending_invoice_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_on_delivery_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_on_delivery_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_pending_return_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_pending_return_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_courier_hold_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_courier_hold_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_nr_1_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_nr_1_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_invoiced_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_invoiced_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_return_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_return_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_incomplete_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_incomplete_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_confirmed_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_confirmed_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_stock_out_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_stock_out_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_partial_delivery_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_partial_delivery_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_lost_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_lost_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['orders']->whereBetween('created_at', [$sd, $ed]);
            } elseif ($request->input('start_date') && $request->input('end_date')) {
                $sd = \Illuminate\Support\Facades\Date::parse($request->input('start_date'))->startOfDay()->toDateTimeString();
                $ed = \Illuminate\Support\Facades\Date::parse($request->input('end_date'))->endOfDay()->toDateTimeString();

                $data['total_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_order_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_hold_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_hold_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_deliver_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_deliver_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_process_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_process_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_pend_pay_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_pend_pay_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_cancel_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_cancel_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_pending_invoice_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_pending_invoice_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_on_delivery_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_on_delivery_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_pending_return_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_pending_return_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_courier_hold_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_courier_hold_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_nr_1_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_nr_1_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_invoiced_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_invoiced_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_return_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_return_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_incomplete_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_incomplete_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_confirmed_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_confirmed_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_stock_out_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_stock_out_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_partial_delivery_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_partial_delivery_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['total_lost_order']->whereBetween('created_at', [$sd, $ed]);
                $data['total_lost_amount']->whereBetween('created_at', [$sd, $ed]);

                $data['orders']->whereBetween('created_at', [$sd, $ed]);
            }

            if ($sts !== null) {
                $data['orders']->where('status', $sts);
            }

            if ($request->input('payment_status') !== null) {
                $data['total_order']->where('payment_status', $request->input('payment_status'));
                $data['total_order_amount']->where('payment_status', $request->input('payment_status'));

                $data['total_hold_order']->where('payment_status', $request->input('payment_status'));
                $data['total_hold_amount']->where('payment_status', $request->input('payment_status'));

                $data['total_deliver_order']->where('payment_status', $request->input('payment_status'));
                $data['total_deliver_amount']->where('payment_status', $request->input('payment_status'));

                $data['total_process_order']->where('payment_status', $request->input('payment_status'));
                $data['total_process_amount']->where('payment_status', $request->input('payment_status'));

                $data['total_pend_pay_order']->where('payment_status', $request->input('payment_status'));
                $data['total_pend_pay_amount']->where('payment_status', $request->input('payment_status'));

                $data['total_cancel_order']->where('payment_status', $request->input('payment_status'));
                $data['total_cancel_amount']->where('payment_status', $request->input('payment_status'));

                $data['total_pending_invoice_order']->where('payment_status', $request->input('payment_status'));
                $data['total_pending_invoice_amount']->where('payment_status', $request->input('payment_status'));

                $data['total_on_delivery_order']->where('payment_status', $request->input('payment_status'));
                $data['total_on_delivery_amount']->where('payment_status', $request->input('payment_status'));

                $data['total_pending_return_order']->where('payment_status', $request->input('payment_status'));
                $data['total_pending_return_amount']->where('payment_status', $request->input('payment_status'));

                $data['total_courier_hold_order']->where('payment_status', $request->input('payment_status'));
                $data['total_courier_hold_amount']->where('payment_status', $request->input('payment_status'));

                $data['total_nr_1_order']->where('payment_status', $request->input('payment_status'));
                $data['total_nr_1_amount']->where('payment_status', $request->input('payment_status'));

                $data['total_invoiced_order']->where('payment_status', $request->input('payment_status'));
                $data['total_invoiced_amount']->where('payment_status', $request->input('payment_status'));

                $data['total_return_order']->where('payment_status', $request->input('payment_status'));
                $data['total_return_amount']->where('payment_status', $request->input('payment_status'));

                $data['total_incomplete_order']->where('payment_status', $request->input('payment_status'));
                $data['total_incomplete_amount']->where('payment_status', $request->input('payment_status'));

                $data['total_confirmed_order']->where('payment_status', $request->input('payment_status'));
                $data['total_confirmed_amount']->where('payment_status', $request->input('payment_status'));

                $data['total_stock_out_order']->where('payment_status', $request->input('payment_status'));
                $data['total_stock_out_amount']->where('payment_status', $request->input('payment_status'));

                $data['total_partial_delivery_order']->where('payment_status', $request->input('payment_status'));
                $data['total_partial_delivery_amount']->where('payment_status', $request->input('payment_status'));

                $data['total_lost_order']->where('payment_status', $request->input('payment_status'));
                $data['total_lost_amount']->where('payment_status', $request->input('payment_status'));

                $data['orders']->where('payment_status', $request->input('payment_status'));
                // dd($data);
            }

            if ($request->input('product_id')) {

                $product_id = $request->input('product_id');
                $data['total_order']->whereHas('get_products', function ($p) use ($product_id): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('products.id', $product_id);
                });
                $data['total_order_amount']->whereHas('get_products', function ($p) use ($product_id): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('products.id', $product_id);
                });

                $data['total_hold_order']->whereHas('get_products', function ($p) use ($product_id): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('products.id', $product_id);
                });
                $data['total_hold_amount']->whereHas('get_products', function ($p) use ($product_id): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('products.id', $product_id);
                });

                $data['total_deliver_order']->whereHas('get_products', function ($p) use ($product_id): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('products.id', $product_id);
                });
                $data['total_deliver_amount']->whereHas('get_products', function ($p) use ($product_id): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('products.id', $product_id);
                });

                $data['total_process_order']->whereHas('get_products', function ($p) use ($product_id): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('products.id', $product_id);
                });
                $data['total_process_amount']->whereHas('get_products', function ($p) use ($product_id): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('products.id', $product_id);
                });

                $data['total_pend_pay_order']->whereHas('get_products', function ($p) use ($product_id): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('products.id', $product_id);
                });
                $data['total_pend_pay_amount']->whereHas('get_products', function ($p) use ($product_id): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('products.id', $product_id);
                });

                $data['total_cancel_order']->whereHas('get_products', function ($p) use ($product_id): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('products.id', $product_id);
                });
                $data['total_cancel_amount']->whereHas('get_products', function ($p) use ($product_id): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('products.id', $product_id);
                });

                $data['total_pending_invoice_order']->whereHas('get_products', function ($p) use ($product_id): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('products.id', $product_id);
                });
                $data['total_pending_invoice_amount']->whereHas('get_products', function ($p) use ($product_id): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('products.id', $product_id);
                });

                $data['total_on_delivery_order']->whereHas('get_products', function ($p) use ($product_id): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('products.id', $product_id);
                });
                $data['total_on_delivery_amount']->whereHas('get_products', function ($p) use ($product_id): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('products.id', $product_id);
                });

                $data['total_pending_return_order']->whereHas('get_products', function ($p) use ($product_id): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('products.id', $product_id);
                });
                $data['total_pending_return_amount']->whereHas('get_products', function ($p) use ($product_id): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('products.id', $product_id);
                });

                $data['total_courier_hold_order']->whereHas('get_products', function ($p) use ($product_id): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('products.id', $product_id);
                });
                $data['total_courier_hold_amount']->whereHas('get_products', function ($p) use ($product_id): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('products.id', $product_id);
                });

                $data['total_nr_1_order']->whereHas('get_products', function ($p) use ($product_id): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('products.id', $product_id);
                });
                $data['total_nr_1_amount']->whereHas('get_products', function ($p) use ($product_id): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('products.id', $product_id);
                });

                $data['total_invoiced_order']->whereHas('get_products', function ($p) use ($product_id): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('products.id', $product_id);
                });
                $data['total_invoiced_amount']->whereHas('get_products', function ($p) use ($product_id): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('products.id', $product_id);
                });

                $data['total_return_order']->whereHas('get_products', function ($p) use ($product_id): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('products.id', $product_id);
                });
                $data['total_return_amount']->whereHas('get_products', function ($p) use ($product_id): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('products.id', $product_id);
                });

                $data['total_incomplete_order']->whereHas('get_products', function ($p) use ($product_id): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('products.id', $product_id);
                });
                $data['total_incomplete_amount']->whereHas('get_products', function ($p) use ($product_id): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('products.id', $product_id);
                });

                $data['total_confirmed_order']->whereHas('get_products', function ($p) use ($product_id): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('products.id', $product_id);
                });
                $data['total_confirmed_amount']->whereHas('get_products', function ($p) use ($product_id): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('products.id', $product_id);
                });

                $data['total_stock_out_order']->whereHas('get_products', function ($p) use ($product_id): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('products.id', $product_id);
                });
                $data['total_stock_out_amount']->whereHas('get_products', function ($p) use ($product_id): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('products.id', $product_id);
                });

                $data['total_partial_delivery_order']->whereHas('get_products', function ($p) use ($product_id): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('products.id', $product_id);
                });
                $data['total_partial_delivery_amount']->whereHas('get_products', function ($p) use ($product_id): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('products.id', $product_id);
                });

                $data['total_lost_order']->whereHas('get_products', function ($p) use ($product_id): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('products.id', $product_id);
                });
                $data['total_lost_amount']->whereHas('get_products', function ($p) use ($product_id): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('products.id', $product_id);
                });

                $data['orders']->whereHas('get_products', function ($p) use ($product_id): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('products.id', $product_id);
                });
            }

            if ($request->input('employee_id')) {

                $employee_id = $request->input('employee_id');
                $data['total_order']->whereHas('get_assigned', function ($p) use ($employee_id): void {
                    $p->where('employee_id', $employee_id);
                });

                $data['total_order_amount']->whereHas('get_assigned', function ($p) use ($employee_id): void {
                    $p->where('employee_id', $employee_id);
                });

                $data['total_hold_order']->whereHas('get_assigned', function ($p) use ($employee_id): void {
                    $p->where('employee_id', $employee_id);
                });
                $data['total_hold_amount']->whereHas('get_assigned', function ($p) use ($employee_id): void {
                    $p->where('employee_id', $employee_id);
                });

                $data['total_deliver_order']->whereHas('get_assigned', function ($p) use ($employee_id): void {
                    $p->where('employee_id', $employee_id);
                });
                $data['total_deliver_amount']->whereHas('get_assigned', function ($p) use ($employee_id): void {
                    $p->where('employee_id', $employee_id);
                });

                $data['total_process_order']->whereHas('get_assigned', function ($p) use ($employee_id): void {
                    $p->where('employee_id', $employee_id);
                });
                $data['total_process_amount']->whereHas('get_assigned', function ($p) use ($employee_id): void {
                    $p->where('employee_id', $employee_id);
                });

                $data['total_pend_pay_order']->whereHas('get_assigned', function ($p) use ($employee_id): void {
                    $p->where('employee_id', $employee_id);
                });
                $data['total_pend_pay_amount']->whereHas('get_assigned', function ($p) use ($employee_id): void {
                    $p->where('employee_id', $employee_id);
                });

                $data['total_cancel_order']->whereHas('get_assigned', function ($p) use ($employee_id): void {
                    $p->where('employee_id', $employee_id);
                });
                $data['total_cancel_amount']->whereHas('get_assigned', function ($p) use ($employee_id): void {
                    $p->where('employee_id', $employee_id);
                });

                $data['total_pending_invoice_order']->whereHas('get_assigned', function ($p) use ($employee_id): void {
                    $p->where('employee_id', $employee_id);
                });
                $data['total_pending_invoice_amount']->whereHas('get_assigned', function ($p) use ($employee_id): void {
                    $p->where('employee_id', $employee_id);
                });

                $data['total_on_delivery_order']->whereHas('get_assigned', function ($p) use ($employee_id): void {
                    $p->where('employee_id', $employee_id);
                });
                $data['total_on_delivery_amount']->whereHas('get_assigned', function ($p) use ($employee_id): void {
                    $p->where('employee_id', $employee_id);
                });

                $data['total_pending_return_order']->whereHas('get_assigned', function ($p) use ($employee_id): void {
                    $p->where('employee_id', $employee_id);
                });
                $data['total_pending_return_amount']->whereHas('get_assigned', function ($p) use ($employee_id): void {
                    $p->where('employee_id', $employee_id);
                });

                $data['total_courier_hold_order']->whereHas('get_assigned', function ($p) use ($employee_id): void {
                    $p->where('employee_id', $employee_id);
                });
                $data['total_courier_hold_amount']->whereHas('get_assigned', function ($p) use ($employee_id): void {
                    $p->where('employee_id', $employee_id);
                });

                $data['total_nr_1_order']->whereHas('get_assigned', function ($p) use ($employee_id): void {
                    $p->where('employee_id', $employee_id);
                });
                $data['total_nr_1_amount']->whereHas('get_assigned', function ($p) use ($employee_id): void {
                    $p->where('employee_id', $employee_id);
                });

                $data['total_invoiced_order']->whereHas('get_assigned', function ($p) use ($employee_id): void {
                    $p->where('employee_id', $employee_id);
                });
                $data['total_invoiced_amount']->whereHas('get_assigned', function ($p) use ($employee_id): void {
                    $p->where('employee_id', $employee_id);
                });

                $data['total_return_order']->whereHas('get_assigned', function ($p) use ($employee_id): void {
                    $p->where('employee_id', $employee_id);
                });
                $data['total_return_amount']->whereHas('get_assigned', function ($p) use ($employee_id): void {
                    $p->where('employee_id', $employee_id);
                });

                $data['total_incomplete_order']->whereHas('get_assigned', function ($p) use ($employee_id): void {
                    $p->where('employee_id', $employee_id);
                });
                $data['total_incomplete_amount']->whereHas('get_assigned', function ($p) use ($employee_id): void {
                    $p->where('employee_id', $employee_id);
                });

                $data['total_confirmed_order']->whereHas('get_assigned', function ($p) use ($employee_id): void {
                    $p->where('employee_id', $employee_id);
                });
                $data['total_confirmed_amount']->whereHas('get_assigned', function ($p) use ($employee_id): void {
                    $p->where('employee_id', $employee_id);
                });

                $data['total_stock_out_order']->whereHas('get_assigned', function ($p) use ($employee_id): void {
                    $p->where('employee_id', $employee_id);
                });
                $data['total_stock_out_amount']->whereHas('get_assigned', function ($p) use ($employee_id): void {
                    $p->where('employee_id', $employee_id);
                });

                $data['total_partial_delivery_order']->whereHas('get_assigned', function ($p) use ($employee_id): void {
                    $p->where('employee_id', $employee_id);
                });
                $data['total_partial_delivery_amount']->whereHas('get_assigned', function ($p) use ($employee_id): void {
                    $p->where('employee_id', $employee_id);
                });

                $data['total_lost_order']->whereHas('get_assigned', function ($p) use ($employee_id): void {
                    $p->where('employee_id', $employee_id);
                });
                $data['total_lost_amount']->whereHas('get_assigned', function ($p) use ($employee_id): void {
                    $p->where('employee_id', $employee_id);
                });

                $data['orders']->whereHas('get_assigned', function ($p) use ($employee_id): void {
                    $p->where('employee_id', $employee_id);
                });
            }

            if ($request->input('query')) {
                $data['orders']->where('customer_phone', 'LIKE', "%{$request->input('query')}%");
                $data['orders']->orWhere('customer_name', 'LIKE', "%{$request->input('query')}%");
                $data['orders']->orWhere('invoice_id', 'LIKE', "%{$request->input('query')}%");
                $data['orders']->orWhere('ip_address', $request->input('query'));
            }

            if ($request->input('courier_status')) {
                $data['orders']->where('courier_status', $request->input('courier_status'));
            }

            if ($request->input('courier_id')) {

                $data['total_order']->where('courier_id', $request->input('courier_id'));
                $data['total_order_amount']->where('courier_id', $request->input('courier_id'));

                $data['total_hold_order']->where('courier_id', $request->input('courier_id'));
                $data['total_hold_amount']->where('courier_id', $request->input('courier_id'));

                $data['total_deliver_order']->where('courier_id', $request->input('courier_id'));
                $data['total_deliver_amount']->where('courier_id', $request->input('courier_id'));

                $data['total_process_order']->where('courier_id', $request->input('courier_id'));
                $data['total_process_amount']->where('courier_id', $request->input('courier_id'));

                $data['total_pend_pay_order']->where('courier_id', $request->input('courier_id'));
                $data['total_pend_pay_amount']->where('courier_id', $request->input('courier_id'));

                $data['total_cancel_order']->where('courier_id', $request->input('courier_id'));
                $data['total_cancel_amount']->where('courier_id', $request->input('courier_id'));

                $data['total_pending_invoice_order']->where('courier_id', $request->input('courier_id'));
                $data['total_pending_invoice_amount']->where('courier_id', $request->input('courier_id'));

                $data['total_on_delivery_order']->where('courier_id', $request->input('courier_id'));
                $data['total_on_delivery_amount']->where('courier_id', $request->input('courier_id'));

                $data['total_pending_return_order']->where('courier_id', $request->input('courier_id'));
                $data['total_pending_return_amount']->where('courier_id', $request->input('courier_id'));

                $data['total_courier_hold_order']->where('courier_id', $request->input('courier_id'));
                $data['total_courier_hold_amount']->where('courier_id', $request->input('courier_id'));

                $data['total_nr_1_order']->where('courier_id', $request->input('courier_id'));
                $data['total_nr_1_amount']->where('courier_id', $request->input('courier_id'));

                $data['total_invoiced_order']->where('courier_id', $request->input('courier_id'));
                $data['total_invoiced_amount']->where('courier_id', $request->input('courier_id'));

                $data['total_return_order']->where('courier_id', $request->input('courier_id'));
                $data['total_return_amount']->where('courier_id', $request->input('courier_id'));

                $data['total_incomplete_order']->where('courier_id', $request->input('courier_id'));
                $data['total_incomplete_amount']->where('courier_id', $request->input('courier_id'));

                $data['total_confirmed_order']->where('courier_id', $request->input('courier_id'));
                $data['total_confirmed_amount']->where('courier_id', $request->input('courier_id'));

                $data['total_stock_out_order']->where('courier_id', $request->input('courier_id'));
                $data['total_stock_out_amount']->where('courier_id', $request->input('courier_id'));

                $data['total_partial_delivery_order']->where('courier_id', $request->input('courier_id'));
                $data['total_partial_delivery_amount']->where('courier_id', $request->input('courier_id'));

                $data['total_lost_order']->where('courier_id', $request->input('courier_id'));
                $data['total_lost_amount']->where('courier_id', $request->input('courier_id'));

                $data['orders']->where('courier_id', $request->input('courier_id'));
                $data['courier_id'] = $request->input('courier_id');
            }

            if ($request->input('shipping_id')) {
                $data['total_order']->where('shipping_method', $request->input('shipping_id'));
                $data['total_order_amount']->where('shipping_method', $request->input('shipping_id'));

                $data['total_hold_order']->where('shipping_method', $request->input('shipping_id'));
                $data['total_hold_amount']->where('shipping_method', $request->input('shipping_id'));

                $data['total_deliver_order']->where('shipping_method', $request->input('shipping_id'));
                $data['total_deliver_amount']->where('shipping_method', $request->input('shipping_id'));

                $data['total_process_order']->where('shipping_method', $request->input('shipping_id'));
                $data['total_process_amount']->where('shipping_method', $request->input('shipping_id'));

                $data['total_pend_pay_order']->where('shipping_method', $request->input('shipping_id'));
                $data['total_pend_pay_amount']->where('shipping_method', $request->input('shipping_id'));

                $data['total_cancel_order']->where('shipping_method', $request->input('shipping_id'));
                $data['total_cancel_amount']->where('shipping_method', $request->input('shipping_id'));

                $data['total_pending_invoice_order']->where('shipping_method', $request->input('shipping_id'));
                $data['total_pending_invoice_amount']->where('shipping_method', $request->input('shipping_id'));

                $data['total_on_delivery_order']->where('shipping_method', $request->input('shipping_id'));
                $data['total_on_delivery_amount']->where('shipping_method', $request->input('shipping_id'));

                $data['total_pending_return_order']->where('shipping_method', $request->input('shipping_id'));
                $data['total_pending_return_amount']->where('shipping_method', $request->input('shipping_id'));

                $data['total_courier_hold_order']->where('shipping_method', $request->input('shipping_id'));
                $data['total_courier_hold_amount']->where('shipping_method', $request->input('shipping_id'));

                $data['total_nr_1_order']->where('shipping_method', $request->input('shipping_id'));
                $data['total_nr_1_amount']->where('shipping_method', $request->input('shipping_id'));

                $data['total_invoiced_order']->where('shipping_method', $request->input('shipping_id'));
                $data['total_invoiced_amount']->where('shipping_method', $request->input('shipping_id'));

                $data['total_return_order']->where('shipping_method', $request->input('shipping_id'));
                $data['total_return_amount']->where('shipping_method', $request->input('shipping_id'));

                $data['total_incomplete_order']->where('shipping_method', $request->input('shipping_id'));
                $data['total_incomplete_amount']->where('shipping_method', $request->input('shipping_id'));

                $data['total_confirmed_order']->where('shipping_method', $request->input('shipping_id'));
                $data['total_confirmed_amount']->where('shipping_method', $request->input('shipping_id'));

                $data['total_stock_out_order']->where('shipping_method', $request->input('shipping_id'));
                $data['total_stock_out_amount']->where('shipping_method', $request->input('shipping_id'));

                $data['total_partial_delivery_order']->where('shipping_method', $request->input('shipping_id'));
                $data['total_partial_delivery_amount']->where('shipping_method', $request->input('shipping_id'));

                $data['total_lost_order']->where('shipping_method', $request->input('shipping_id'));
                $data['total_lost_amount']->where('shipping_method', $request->input('shipping_id'));

                $data['orders']->where('shipping_method', $request->input('shipping_id'));
                $data['shipping_id'] = $request->input('shipping_id');
            }
            $data['total_trash_order'] = Order::onlyTrashed()->count();

            $data['total_order'] = $data['total_order']->count();
            $data['total_order_amount'] = $data['total_order_amount']->select(DB::raw('SUM(sub_total - discount) as total_amount'))->first()->total_amount;

            $data['total_hold_order'] = $data['total_hold_order']->count();
            $data['total_hold_amount'] = $data['total_hold_amount']->select(DB::raw('SUM(sub_total - discount) as total_amount'))->first()->total_amount;

            $data['total_deliver_order'] = $data['total_deliver_order']->count();
            $data['total_deliver_amount'] = $data['total_deliver_amount']->select(DB::raw('SUM(sub_total - discount) as total_amount'))->first()->total_amount;

            $data['total_process_order'] = $data['total_process_order']->count();
            $data['total_process_amount'] = $data['total_process_amount']->select(DB::raw('SUM(sub_total - discount) as total_amount'))->first()->total_amount;

            $data['total_pend_pay_order'] = $data['total_pend_pay_order']->count();
            $data['total_pend_pay_amount'] = $data['total_pend_pay_amount']->select(DB::raw('SUM(sub_total - discount) as total_amount'))->first()->total_amount;

            $data['total_cancel_order'] = $data['total_cancel_order']->count();
            $data['total_cancel_amount'] = $data['total_cancel_amount']->select(DB::raw('SUM(sub_total - discount) as total_amount'))->first()->total_amount;

            $data['total_pending_invoice_order'] = $data['total_pending_invoice_order']->count();
            $data['total_pending_invoice_amount'] = $data['total_pending_invoice_amount']->select(DB::raw('SUM(sub_total - discount) as total_amount'))->first()->total_amount;

            $data['total_on_delivery_order'] = $data['total_on_delivery_order']->count();
            $data['total_on_delivery_amount'] = $data['total_on_delivery_amount']->select(DB::raw('SUM(sub_total - discount) as total_amount'))->first()->total_amount;

            $data['total_pending_return_order'] = $data['total_pending_return_order']->count();
            $data['total_pending_return_amount'] = $data['total_pending_return_amount']->select(DB::raw('SUM(sub_total - discount) as total_amount'))->first()->total_amount;

            $data['total_courier_hold_order'] = $data['total_courier_hold_order']->count();
            $data['total_courier_hold_amount'] = $data['total_courier_hold_amount']->select(DB::raw('SUM(sub_total - discount) as total_amount'))->first()->total_amount;

            $data['total_nr_1_order'] = $data['total_nr_1_order']->count();
            $data['total_nr_1_amount'] = $data['total_nr_1_amount']->select(DB::raw('SUM(sub_total - discount) as total_amount'))->first()->total_amount;

            $data['total_invoiced_order'] = $data['total_invoiced_order']->count();
            $data['total_invoiced_amount'] = $data['total_invoiced_amount']->select(DB::raw('SUM(sub_total - discount) as total_amount'))->first()->total_amount;

            $data['total_return_order'] = $data['total_return_order']->count();
            $data['total_return_amount'] = $data['total_return_amount']->select(DB::raw('SUM(sub_total - discount) as total_amount'))->first()->total_amount;

            $data['total_incomplete_order'] = $data['total_incomplete_order']->count();
            $data['total_incomplete_amount'] = $data['total_incomplete_amount']->select(DB::raw('SUM(sub_total - discount) as total_amount'))->first()->total_amount;

            $data['total_confirmed_order'] = $data['total_confirmed_order']->count();
            $data['total_confirmed_amount'] = $data['total_confirmed_amount']->select(DB::raw('SUM(sub_total - discount) as total_amount'))->first()->total_amount;

            $data['total_stock_out_order'] = $data['total_stock_out_order']->count();
            $data['total_stock_out_amount'] = $data['total_stock_out_amount']->select(DB::raw('SUM(sub_total - discount) as total_amount'))->first()->total_amount;

            $data['total_partial_delivery_order'] = $data['total_partial_delivery_order']->count();
            $data['total_partial_delivery_amount'] = $data['total_partial_delivery_amount']->select(DB::raw('SUM(sub_total - discount) as total_amount'))->first()->total_amount;

            $data['total_lost_order'] = $data['total_lost_order']->count();
            $data['total_lost_amount'] = $data['total_lost_amount']->select(DB::raw('SUM(sub_total - discount) as total_amount'))->first()->total_amount;

            $data['count'] = $data['orders']->count();
            $data['orders'] = $data['orders']->with('get_products.get_product', 'get_courier', 'get_assigned.get_employee')
                ->select('carrybee_consignment_id', 'source', 'courier_api_response', 'courier_status_reason', 'customer_activity', 'is_fake', 'invoice_id', 'customer_name', 'customer_phone', 'customer_address', 'total', 'order_date', 'created_at', 'status', 'staff_note', 'courier_note', 'courier_status', 'id', 'ip_address', 'courier_id', 'paid', 'due', 'pathao_consignment_id', 'redx_tracking_id', 'payment_status')
                ->orderBy('id', 'desc')->paginate($paginate);
            $data['orders']->appends([
                'paginate' => $paginate,
                'query' => $request->input('query'),
                'status' => $request->input('status'),
                'custom_range' => $request->input('custom_range'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),

            ]);
            // dd($data['amount']);
            // dd($data);
        } elseif (Auth::guard('employee')->check()) {
            $data['total_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where('order_assigns.employee_id', Auth::guard('employee')->id());
            $data['total_hold_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 0]]);
            $data['total_deliver_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 1]]);
            $data['total_process_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 2]]);
            $data['total_pend_pay_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 3]]);
            $data['total_cancel_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 4]]);
            $data['total_pending_invoice_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 5]]);
            $data['total_on_delivery_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 6]]);
            $data['total_pending_return_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 7]]);
            $data['total_courier_hold_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 8]]);
            $data['total_nr_1_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 9]]);
            $data['total_invoiced_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 10]]);

            $data['total_return_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 11]]);
            $data['total_incomplete_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 12]]);
            $data['total_confirmed_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 13]]);
            $data['total_stock_out_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 14]]);
            $data['total_partial_delivery_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 15]]);
            $data['total_lost_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 16]]);

            $data['orders'] = Order::query();

            if ($custom_range == 'today') {
                $custom_range = \Illuminate\Support\Facades\Date::today()->toDateTimeString();

                $data['total_order']->whereDate('orders.created_at', $custom_range);
                $data['total_hold_order']->whereDate('orders.created_at', $custom_range);
                $data['total_deliver_order']->whereDate('orders.created_at', $custom_range);
                $data['total_process_order']->whereDate('orders.created_at', $custom_range);
                $data['total_pend_pay_order']->whereDate('orders.created_at', $custom_range);
                $data['total_cancel_order']->whereDate('orders.created_at', $custom_range);
                $data['total_pending_invoice_order']->whereDate('orders.created_at', $custom_range);
                $data['total_on_delivery_order']->whereDate('orders.created_at', $custom_range);
                $data['total_pending_return_order']->whereDate('orders.created_at', $custom_range);
                $data['total_courier_hold_order']->whereDate('orders.created_at', $custom_range);
                $data['total_nr_1_order']->whereDate('orders.created_at', $custom_range);
                $data['total_invoiced_order']->whereDate('orders.created_at', $custom_range);
                $data['total_return_order']->whereDate('orders.created_at', $custom_range);
                $data['total_incomplete_order']->whereDate('orders.created_at', $custom_range);
                $data['total_confirmed_order']->whereDate('orders.created_at', $custom_range);
                $data['total_stock_out_order']->whereDate('orders.created_at', $custom_range);
                $data['total_partial_delivery_order']->whereDate('orders.created_at', $custom_range);
                $data['total_lost_order']->whereDate('orders.created_at', $custom_range);

                $data['orders']->whereDate('orders.created_at', $custom_range);
            } elseif ($custom_range == 'yesterday') {
                $custom_range = \Illuminate\Support\Facades\Date::yesterday()->toDateTimeString();

                $data['total_order']->whereDate('orders.created_at', $custom_range);
                $data['total_hold_order']->whereDate('orders.created_at', $custom_range);
                $data['total_deliver_order']->whereDate('orders.created_at', $custom_range);
                $data['total_process_order']->whereDate('orders.created_at', $custom_range);
                $data['total_pend_pay_order']->whereDate('orders.created_at', $custom_range);
                $data['total_cancel_order']->whereDate('orders.created_at', $custom_range);
                $data['total_pending_invoice_order']->whereDate('orders.created_at', $custom_range);
                $data['total_on_delivery_order']->whereDate('orders.created_at', $custom_range);
                $data['total_pending_return_order']->whereDate('orders.created_at', $custom_range);
                $data['total_courier_hold_order']->whereDate('orders.created_at', $custom_range);
                $data['total_nr_1_order']->whereDate('orders.created_at', $custom_range);
                $data['total_invoiced_order']->whereDate('orders.created_at', $custom_range);
                $data['total_return_order']->whereDate('orders.created_at', $custom_range);
                $data['total_incomplete_order']->whereDate('orders.created_at', $custom_range);
                $data['total_confirmed_order']->whereDate('orders.created_at', $custom_range);
                $data['total_stock_out_order']->whereDate('orders.created_at', $custom_range);
                $data['total_partial_delivery_order']->whereDate('orders.created_at', $custom_range);
                $data['total_lost_order']->whereDate('orders.created_at', $custom_range);

                $data['orders']->whereDate('orders.created_at', $custom_range);
            } elseif ($custom_range == 'last_7_days') {
                $custom_range = \Illuminate\Support\Facades\Date::now()->subDays(7)->toDateTimeString();

                $data['total_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_hold_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_deliver_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_process_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_pend_pay_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_cancel_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_pending_invoice_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_on_delivery_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_pending_return_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_courier_hold_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_nr_1_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_invoiced_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_return_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_incomplete_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_confirmed_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_stock_out_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_partial_delivery_order']->where('orders.created_at', '>=', $custom_range);
                $data['total_lost_order']->where('orders.created_at', '>=', $custom_range);

                $data['orders']->where('orders.created_at', '>=', $custom_range);
            } elseif ($custom_range == 'this_month') {
                $custom_range = \Illuminate\Support\Facades\Date::now()->month;

                $data['total_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_hold_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_deliver_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_process_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_pend_pay_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_cancel_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_pending_invoice_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_on_delivery_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_pending_return_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_courier_hold_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_nr_1_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_invoiced_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_return_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_incomplete_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_confirmed_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_stock_out_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_partial_delivery_order']->whereMonth('orders.created_at', $custom_range);
                $data['total_lost_order']->whereMonth('orders.created_at', $custom_range);

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
                $data['total_pending_invoice_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_on_delivery_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_pending_return_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_courier_hold_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_nr_1_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_invoiced_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_return_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_incomplete_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_confirmed_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_stock_out_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_partial_delivery_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_lost_order']->whereBetween('orders.created_at', [$sd, $ed]);

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
                $data['total_pending_invoice_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_on_delivery_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_pending_return_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_courier_hold_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_nr_1_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_invoiced_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_return_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_incomplete_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_confirmed_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_stock_out_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_partial_delivery_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_lost_order']->whereBetween('orders.created_at', [$sd, $ed]);

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
                $data['total_pending_invoice_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_on_delivery_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_pending_return_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_courier_hold_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_nr_1_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_invoiced_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_return_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_incomplete_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_confirmed_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_stock_out_order']->whereBetween('orders.created_at', [$sd, $ed]);
                $data['total_partial_delivery_order']->whereBetween('orders.created_at', [$sd, $ed]);
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
            $data['total_hold_order'] = $data['total_hold_order']->count();
            $data['total_deliver_order'] = $data['total_deliver_order']->count();
            $data['total_process_order'] = $data['total_process_order']->count();
            $data['total_pend_pay_order'] = $data['total_pend_pay_order']->count();
            $data['total_cancel_order'] = $data['total_cancel_order']->count();
            $data['total_pending_invoice_order'] = $data['total_pending_invoice_order']->count();
            $data['total_on_delivery_order'] = $data['total_on_delivery_order']->count();
            $data['total_pending_return_order'] = $data['total_pending_return_order']->count();
            $data['total_courier_hold_order'] = $data['total_courier_hold_order']->count();
            $data['total_nr_1_order'] = $data['total_nr_1_order']->count();
            $data['total_invoiced_order'] = $data['total_invoiced_order']->count();
            $data['total_return_order'] = $data['total_return_order']->count();
            $data['total_incomplete_order'] = $data['total_incomplete_order']->count();
            $data['total_confirmed_order'] = $data['total_confirmed_order']->count();
            $data['total_stock_out_order'] = $data['total_stock_out_order']->count();
            $data['total_partial_delivery_order'] = $data['total_partial_delivery_order']->count();
            $data['total_lost_order'] = $data['total_lost_order']->count();

            $data['count'] = $data['orders']->whereHas('get_assigned', function ($qry): void {
                $qry->where('employee_id', Auth::guard('employee')->id());
            })->count();

            $data['orders'] = $data['orders']->with('get_products.get_product', 'get_courier', 'get_assigned.get_employee')
                ->select('carrybee_consignment_id', 'source', 'courier_api_response', 'courier_status_reason', 'customer_activity', 'is_fake', 'invoice_id', 'customer_name', 'customer_phone', 'customer_address', 'total', 'order_date', 'created_at', 'status', 'staff_note', 'courier_note', 'courier_status', 'id', 'ip_address', 'courier_id', 'paid', 'due', 'pathao_consignment_id', 'redx_tracking_id')
                ->whereHas('get_assigned', function ($qry): void {
                    $qry->where('employee_id', Auth::guard('employee')->id());
                })->orderBy('id', 'desc')->paginate($paginate);
            $data['orders']->appends([
                'paginate' => $paginate,
                'query' => $request->input('query'),
                'status' => $request->input('status'),
                'custom_range' => $request->input('custom_range'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
            ]);
        } else {
            $data = [];
        }
        // dd($data['orders']);
        // dd($data);
        $products = Product::orderBy('id', 'desc')->pluck('name', 'id');

        return view('backEnd.admin.orders.index', compact('products', 'data', 'query', 'status', 'sts'));
    }

    public function create()
    {
        $products = Product::pluck('name', 'id');
        $courier = Courier::where('status', 1)->pluck('courier_name', 'id');
        $shipping = ShippingMethod::where('status', 1)->pluck('type', 'id');
        if (Order::withTrashed()->count() > 0) {
            $invoice_id = Order::withTrashed()->latest('id')->first()->invoice_id;
            $invoice_id = trim((string) $invoice_id, 'INV');
            $invoice_id++;
            $invoice_id = 'INV'.$invoice_id;
        } else {
            $invoice_id = 'INV1';
        }

        return view('backEnd.admin.orders.add', compact('products', 'courier', 'invoice_id', 'shipping'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        if (Order::withTrashed()->count() > 0) {
            $invoice_id = Order::withTrashed()->latest('id')->first()->invoice_id;
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

        $sms = SmsSetting::where('status', $order_id->status)->first();
        // send whatsapp
        if ($sms && $sms->is_whatsapp == 1 && $sms->template_name != null) {
            $this->WpServices->sendOrderWhatsapp($order_id, $sms->template_name, $sms->status);
        }
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
            $purchase_cost = Product::where('id', $item)->first()->purchase_cost;
            OrderProduct::create([
                'order_id' => $order_id->id,
                'product_id' => $item,
                'qty' => $request->qty[$key],
                'price' => $request->price[$key],
                'purchase_cost' => $purchase_cost,
                'attributes' => count($attrb) > 0 ? $attrb[0] : null,
                'attribute_ids' => count($attrb) > 0 ? $attrb[1] : null,
            ]);
        }

        if ($request->courier_id == 1) {
            // pathao courier entry
            $couriers = Courier::where('id', 1)->first();
            if ($request->shipping_area == 1) {
                $order_id->update([
                    'courier_charge_cost' => $couriers->courier_charge_isd > 0 ? $couriers->courier_charge_isd : 0,
                ]);
            }
            if ($request->shipping_area == 2) {
                $order_id->update([
                    'courier_charge_cost' => $couriers->courier_charge_osd > 0 ? $couriers->courier_charge_osd : 0,
                ]);
            }
        } elseif ($request->courier_id == 2) {
            $couriers = Courier::where('id', 2)->first();
            if ($request->shipping_area == 1) {
                $order_id->update([
                    'courier_charge_cost' => $couriers->courier_charge_isd > 0 ? $couriers->courier_charge_isd : 0,
                ]);
            }
            if ($request->shipping_area == 2) {
                $order_id->update([
                    'courier_charge_cost' => $couriers->courier_charge_osd > 0 ? $couriers->courier_charge_osd : 0,
                ]);
            }
        } elseif ($request->courier_id == 3) {
            $couriers = Courier::where('id', 3)->first();
            if ($request->shipping_area == 1) {
                $order_id->update([
                    'courier_charge_cost' => $couriers->courier_charge_isd > 0 ? $couriers->courier_charge_isd : 0,
                ]);
            }
            if ($request->shipping_area == 2) {
                $order_id->update([
                    'courier_charge_cost' => $couriers->courier_charge_osd > 0 ? $couriers->courier_charge_osd : 0,
                ]);
            }
        }

        // if ($request->status == 5) {
        //     if ($request->courier_id == 1) {
        //         //pathao courier entry
        //         $couriers = Courier::where('id', 1)->first();
        //         if ($request->shipping_area == 1) {
        //             $order_id->update([
        //                 'courier_charge_cost' => $couriers->courier_charge_isd,
        //             ]);
        //         }
        //         if ($request->shipping_area == 2) {
        //             $order_id->update([
        //                 'courier_charge_cost' => $couriers->courier_charge_osd,
        //             ]);
        //         }

        //         $credential = DB::table('pathao_apis')->select('is_active', 'access_token', 'store_id')->where('id', 1)->first();
        //         if ($credential->is_active == 1) {
        //             $url              = 'https://api-hermes.pathao.com/aladdin/api/v1/orders';
        //             $item_description = "";
        //             foreach ($order_id->get_products as $key => $get_product) {
        //                 $item_description .= $get_product->get_product->name . "\n";
        //             }
        //             $curl = curl_init();
        //             $vars = [
        //                 'store_id'            => $credential->store_id,
        //                 'merchant_order_id'   => $order_id->invoice_id ?? null,
        //                 'sender_name'         => env('APP_NAME'),
        //                 //'sender_phone' => null,
        //                 'recipient_name'      => $order_id->customer_name ?? null,
        //                 'recipient_phone'     => $order_id->customer_phone ?? null,
        //                 'recipient_address'   => $order_id->customer_address ?? null,
        //                 'recipient_city'      => $order_id->courier_city_id ?? null,
        //                 'recipient_zone'      => $order_id->courier_zone_id ?? null,
        //                 'recipient_area'      => null,
        //                 'delivery_type'       => 48,
        //                 'item_type'           => 2,
        //                 'special_instruction' => null,
        //                 'item_quantity'       => $order_id->get_products->sum('qty') ?? 1,
        //                 'item_weight'         => 0.5,
        //                 'amount_to_collect'   => $order_id->due ?? 0,
        //                 'item_description'    => $item_description ?? null,
        //             ];
        //             $headers = [
        //                 'accept: application/json',
        //                 'content-type: application/json',
        //                 'authorization: Bearer ' . $credential->access_token,
        //             ];
        //             //dd($vars);
        //             $json_string = json_encode($vars);
        //             //dd($json_string);
        //             curl_setopt($curl, CURLOPT_URL, $url);
        //             curl_setopt($curl, CURLOPT_POST, true);
        //             curl_setopt($curl, CURLOPT_POSTFIELDS, $json_string);
        //             curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        //             curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        //             $data = curl_exec($curl);
        //             $data = json_decode($data, true);
        //             curl_close($curl);
        //             if ($data['code'] != 200) {
        //                 $date = Carbon::now() . "\n";
        //                 $fp   = fopen(base_path('storage/logs/pathao_entry_log.txt'), 'a'); //opens file in append mode
        //                 fwrite($fp, $date . json_encode($data) . "\n\n");
        //                 fclose($fp);
        //             }
        //             //dd($data['data']->consignment_id);

        //             $order_id->update([
        //                 'pathao_consignment_id' => $data['code'] == 200 ? $data['data']['consignment_id'] : null,
        //             ]);
        //         }

        //     } elseif ($request->courier_id == 2) {
        //         //redx courier entry
        //         $redx_credential = DB::table('redx_apis')->select('is_active', 'access_token')->where('id', 1)->first();
        //         if ($redx_credential->is_active == 1) {
        //             //get delivery_area
        //             $curl = curl_init();

        //             curl_setopt_array($curl, [
        //                 CURLOPT_URL            => 'https://openapi.redx.com.bd/v1.0.0-beta/areas',
        //                 CURLOPT_RETURNTRANSFER => true,
        //                 CURLOPT_ENCODING       => '',
        //                 CURLOPT_MAXREDIRS      => 10,
        //                 CURLOPT_TIMEOUT        => 0,
        //                 CURLOPT_FOLLOWLOCATION => true,
        //                 CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        //                 CURLOPT_CUSTOMREQUEST  => 'GET',
        //                 CURLOPT_HTTPHEADER     => [
        //                     'API-ACCESS-TOKEN: Bearer ' . $redx_credential->access_token,
        //                 ],
        //             ]);
        //             $response = curl_exec($curl);
        //             curl_close($curl);
        //             $delivery_areas = '';
        //             foreach (json_decode($response, true)['areas'] as $delivery_area) {
        //                 if ($delivery_area['id'] == $order_id->courier_city_id) {
        //                     $delivery_areas = $delivery_area['name'];
        //                     break;
        //                 }
        //             }

        //             //store order into redx
        //             $url  = 'https://openapi.redx.com.bd/v1.0.0-beta/parcel';
        //             $curl = curl_init();
        //             $vars = [
        //                 "customer_name"          => $order_id->customer_name ?? null,
        //                 "customer_phone"         => $order_id->customer_phone ?? null,
        //                 "delivery_area"          => $delivery_areas ?? null,
        //                 "delivery_area_id"       => $order_id->courier_city_id ?? null,
        //                 "customer_address"       => $order_id->customer_address ?? null,
        //                 "merchant_invoice_id"    => $order_id->invoice_id ?? null,
        //                 "cash_collection_amount" => $order_id->due ?? 0,
        //                 "parcel_weight"          => 500,
        //                 "instruction"            => "",
        //                 "value"                  => $order_id->due ?? 0,
        //             ];
        //             $headers = [
        //                 'API-ACCESS-TOKEN: Bearer ' . $redx_credential->access_token,
        //                 'Content-Type: application/json',
        //             ];
        //             $json_string = json_encode($vars);
        //             //dd($json_string);
        //             curl_setopt_array($curl, [
        //                 CURLOPT_HTTPHEADER     => $headers,
        //                 CURLOPT_URL            => $url,
        //                 CURLOPT_RETURNTRANSFER => true,
        //                 CURLOPT_ENCODING       => '',
        //                 CURLOPT_MAXREDIRS      => 10,
        //                 CURLOPT_TIMEOUT        => 0,
        //                 CURLOPT_FOLLOWLOCATION => true,
        //                 CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        //                 CURLOPT_CUSTOMREQUEST  => 'POST',
        //                 CURLOPT_POSTFIELDS     => $json_string,

        //             ]);
        //             $response = curl_exec($curl);
        //             curl_close($curl);

        //             $order_id->update([
        //                 'redx_tracking_id' => json_decode($response, true)['tracking_id'] ?? null,
        //             ]);
        //         }
        //     } elseif ($request->courier_id == 3) {
        //         //steadfast courier entry
        //         $credential = DB::table('stead_fast_apis')->select('is_active', 'api_key', 'secret_key')->where('id', 1)->first();
        //         if ($credential->is_active == 1) {
        //             $vars = [
        //                 'invoice'           => $order_id->invoice_id ?? null,
        //                 'recipient_name'    => $order_id->customer_name ?? null,
        //                 'recipient_address' => $order_id->customer_address ?? null,
        //                 'recipient_phone'   => $order_id->customer_phone ?? null,
        //                 'cod_amount'        => $order_id->due ?? 0,
        //                 'note'              => '',
        //             ];
        //             $json_string = json_encode($vars);
        //             $headers     = [
        //                 'Api-Key: ' . $credential->api_key,
        //                 'Secret-Key: ' . $credential->secret_key,
        //                 'Content-Type: application/json',
        //             ];
        //             //dd($headers);
        //             $curl = curl_init();

        //             curl_setopt_array($curl, [
        //                 CURLOPT_URL            => 'https://portal.steadfast.com.bd/api/v1/create_order',
        //                 CURLOPT_RETURNTRANSFER => true,
        //                 CURLOPT_ENCODING       => '',
        //                 CURLOPT_MAXREDIRS      => 10,
        //                 CURLOPT_TIMEOUT        => 0,
        //                 CURLOPT_FOLLOWLOCATION => true,
        //                 CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        //                 CURLOPT_CUSTOMREQUEST  => 'POST',
        //                 CURLOPT_POSTFIELDS     => $json_string,
        //                 CURLOPT_HTTPHEADER     => $headers,
        //             ]);
        //             $data = curl_exec($curl);
        //             curl_close($curl);

        //             if (json_decode($data)->status != 200) {
        //                 $date = Carbon::now() . "\n";
        //                 $fp   = fopen(base_path('storage/logs/stead_fast_entry_log.txt'), 'a'); //opens file in append mode
        //                 fwrite($fp, $date . json_encode($data) . "\n\n");
        //                 fclose($fp);
        //             }

        //             if (json_decode($data)->status == 200) {
        //                 $order_id->update([
        //                     'status'                    => $request->status,
        //                     'stead_fast_consignment_id' => json_decode($data)->status == 200 ? json_decode($data)->consignment->tracking_code : null,
        //                 ]);
        //             }
        //         }
        //     }
        // }

        // create transaction
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            $created_by = 'admin';
        } elseif (Auth::guard('manager')->check()) {
            $user = Auth::guard('manager')->user();
            $created_by = 'manager';
        } elseif (Auth::guard('employee')->check()) {
            $user = Auth::guard('employee')->user();
            $created_by = 'employee';
        }

        if (Auth::guard('admin')->check()) {
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
                'local',
                $order_id->id,
                strtr(config('transaction_texts.new_order'), [
                    '{user_name}' => $user->name,
                    '{role}' => $created_by,
                    '{employee_name}' => $emp->name,
                ]),
                null,
                $created_by,
                $user->id,
                $i
            );

            return to_route('admin.orders')->with('success', 'Order Created Successfully');
        } elseif (Auth::guard('manager')->check()) {
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
                'local',
                $order_id->id,
                strtr(config('transaction_texts.new_order'), [
                    '{user_name}' => $user->name,
                    '{role}' => $created_by,
                    '{employee_name}' => $emp->name,
                ]),
                null,
                $created_by,
                $user->id,
                $i
            );

            return to_route('manager.orders')->with('success', 'Order Created Successfully');
        } elseif (Auth::guard('employee')->check()) {
            $emp = Auth::guard('employee')->user();
            OrderAssign::create([
                'order_id' => $order_id->id,
                'employee_id' => $emp->id,
            ]);

            // create transaction
            order_transaction(
                'local',
                $order_id->id,
                strtr(config('transaction_texts.new_order'), [
                    '{user_name}' => $user->name,
                    '{role}' => $created_by,
                    '{employee_name}' => $emp->name,
                ]),
                null,
                $created_by,
                $user->id,
                $emp->id
            );

            return to_route('employee.orders')->with('success', 'Order Created Successfully');
        } else {
            return back()->with('warning', 'Something Went Wrong');
        }
    }

    public function edit($id)
    {
        // dd($id);
        $products = Product::pluck('name', 'id');

        $data = Order::with('get_transactions', 'get_products.get_product', 'get_note_history', 'get_customer')->find($id);
        $courier = Courier::where('status', 1)->pluck('courier_name', 'id');
        if ($data->courier_id == 1) { // 1=pathao
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
            $courier_city = $data1;

            // dd($credential);
            $url = 'https://api-hermes.pathao.com/aladdin/api/v1/cities/'.$data->courier_city_id.'/zone-list';
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
            $courier_zone = $data2;
        } elseif ($data->courier_id == 2) { // 2=redx
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
            $courier_city = $data1;

            $courier_zone = [];
        } elseif ($data->courier_id == 4) { // 4=carrybee
            $credential = DB::table('carry_bee_apis')->select('is_active', 'access_token')->where('id', 1)->first();
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
            $d1 = curl_exec($curl);
            $d1 = json_decode($d1, true);
            // curl_close($curl);

            $data1 = [];
            foreach ($d1['data']['data'] as $item) {
                $data1[$item['city_id']] = $item['city_name'];
            }
            $courier_city = $data1;

            // dd($credential);
            $url = 'https://developers.carrybee.com/api/cities/'.$data->courier_city_id.'/zones';
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
            $courier_zone = $data2;
        } else {
            $courier_city = CourierCity::where([['status', 1], ['courier_id', $data->courier_id]])->pluck('city_name', 'id');
            $courier_zone = CourierZone::where([['status', 1], ['courier_id', $data->courier_id]])->pluck('zone_name', 'id');
        }

        return view('backEnd.admin.orders.edit', compact('data', 'products', 'courier', 'courier_city', 'courier_zone'));
        //        return view('backEnd.admin.orders.edit', compact('data', 'products', 'courier'));
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        if ($request->product_id) {
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
                    'price' => $request->price[$key],
                    'purchase_cost' => Product::where('id', $item)->first()->purchase_cost,
                    'attributes' => count($attrb) > 0 ? $attrb[0] : null,
                    'attribute_ids' => count($attrb) > 0 ? $attrb[1] : null,
                ]);
            }

            // create transaction
            if (Auth::guard('admin')->check()) {
                $user = Auth::guard('admin')->user();
                $created_by = 'admin';
            } elseif (Auth::guard('manager')->check()) {
                $user = Auth::guard('manager')->user();
                $created_by = 'manager';
            } elseif (Auth::guard('employee')->check()) {
                $user = Auth::guard('employee')->user();
                $created_by = 'employee';
            }

            order_transaction(
                'local',
                $id,
                strtr(config('transaction_texts.update_order'), [
                    '{user_name}' => $user->name,
                    '{role}' => $created_by,
                ]),
                null,
                $created_by,
                $user->id,
                null
            );

            if ($request->courier_id == 1) {
                // pathao courier entry
                $couriers = Courier::where('id', 1)->first();
                if ($request->shipping_area == 1) {
                    $order_id->update([
                        'courier_charge_cost' => $couriers->courier_charge_isd > 0 ? $couriers->courier_charge_isd : 0,
                    ]);
                }
                if ($request->shipping_area == 2) {
                    $order_id->update([
                        'courier_charge_cost' => $couriers->courier_charge_osd > 0 ? $couriers->courier_charge_osd : 0,
                    ]);
                }
            } elseif ($request->courier_id == 2) {
                $couriers = Courier::where('id', 2)->first();
                if ($request->shipping_area == 1) {
                    $order_id->update([
                        'courier_charge_cost' => $couriers->courier_charge_isd > 0 ? $couriers->courier_charge_isd : 0,
                    ]);
                }
                if ($request->shipping_area == 2) {
                    $order_id->update([
                        'courier_charge_cost' => $couriers->courier_charge_osd > 0 ? $couriers->courier_charge_osd : 0,
                    ]);
                }
            } elseif ($request->courier_id == 3) {
                $couriers = Courier::where('id', 3)->first();
                if ($request->shipping_area == 1) {
                    $order_id->update([
                        'courier_charge_cost' => $couriers->courier_charge_isd > 0 ? $couriers->courier_charge_isd : 0,
                    ]);
                }
                if ($request->shipping_area == 2) {
                    $order_id->update([
                        'courier_charge_cost' => $couriers->courier_charge_osd > 0 ? $couriers->courier_charge_osd : 0,
                    ]);
                }
            }

            $web_settings = DB::table('web_settings')->where('id', 1)->first();
            if ($request->old_status != 5 && $request->status == 5) {
                // send order confirm sms
                if ($web_settings->is_order_confirm_sms == 1) {
                    $products = '';
                    foreach ($order_id->get_products as $key => $item) {
                        if ($key != 0) {
                            $products .= "\n";
                        }
                        $products .= $item->get_product->name.'.';
                    }

                    $mgs_body = strtr($web_settings->order_confirm_sms, [
                        '{$invoice_id}' => $order_id->invoice_id ?? null,
                        '{$products}' => $products ?? null,
                        '{$total_amount}' => $order_id->total ?? 0,
                    ]);

                    // dd($mgs_body);
                    $apikey = config('app.sms_api_key');
                    // $sender = config('app.sms_sender');

                    $msisdn = ltrim((string) BanglaToEnglishConverter::bn2en($order_id->customer_phone), '+');
                    // dd($apikey, $msisdn, $text);
                    $curl = curl_init();

                    curl_setopt_array($curl, [
                        CURLOPT_URL => 'https://api.sms.net.bd/sendsms',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => ['api_key' => $apikey, 'msg' => $mgs_body, 'to' => $msisdn],
                    ]);

                    $response = curl_exec($curl);

                    curl_close($curl);
                    // dd($response);
                }

                /*if ($request->courier_id == 1) {
                    //pathao courier entry
                    $credential = DB::table('pathao_apis')->select('is_active', 'access_token', 'store_id')->where('id', 1)->first();
                    if ($credential->is_active == 1) {
                        $url = 'https://api-hermes.pathao.com/aladdin/api/v1/orders';
                        $item_description = "";
                        foreach ($order_id->get_products as $key => $get_product) {
                            $item_description .= $get_product->get_product->name . "\n";
                        }
                        $curl = curl_init();
                        $vars = [
                            'store_id' => $credential->store_id,
                            'merchant_order_id' => $order_id->invoice_id ?? null,
                            'sender_name' => env('APP_NAME'),
                            //'sender_phone' => null,
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
                            'authorization: Bearer ' . $credential->access_token,
                        ];
                        //dd($vars);
                        $json_string = json_encode($vars);
                        //dd($json_string);
                        curl_setopt($curl, CURLOPT_URL, $url);
                        curl_setopt($curl, CURLOPT_POST, true);
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $json_string);
                        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        $data = curl_exec($curl);
                        $data = json_decode($data, true);
                        curl_close($curl);
                        if ($data['code'] != 200) {
                            $date = Carbon::now() . "\n";
                            $fp = fopen(base_path('storage/logs/pathao_entry_log.txt'), 'a'); //opens file in append mode
                            fwrite($fp, $date . json_encode($data) . "\n\n");
                            fclose($fp);
                        }
                        //dd($data['data']->consignment_id);

                        $order_id->update([
                            'status' => $request->status,
                            'pathao_consignment_id' => $data['code'] == 200 ? $data['data']['consignment_id'] : null,
                        ]);
                    }
                } elseif ($request->courier_id == 2) {
                    //redx courier entry
                    $redx_credential = DB::table('redx_apis')->select('is_active', 'access_token')->where('id', 1)->first();
                    if ($redx_credential->is_active == 1) {
                        //get delivery_area
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
                                'API-ACCESS-TOKEN: Bearer ' . $redx_credential->access_token,
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

                        //store order into redx
                        $url = 'https://openapi.redx.com.bd/v1.0.0-beta/parcel';
                        $curl = curl_init();
                        $vars = [
                            "customer_name" => $order_id->customer_name ?? null,
                            "customer_phone" => $order_id->customer_phone ?? null,
                            "delivery_area" => $delivery_areas ?? null,
                            "delivery_area_id" => $order_id->courier_city_id ?? null,
                            "customer_address" => $order_id->customer_address ?? null,
                            "merchant_invoice_id" => $order_id->invoice_id ?? null,
                            "cash_collection_amount" => $order_id->due ?? 0,
                            "parcel_weight" => 500,
                            "instruction" => "",
                            "value" => $order_id->due ?? 0,
                        ];
                        $headers = [
                            'API-ACCESS-TOKEN: Bearer ' . $redx_credential->access_token,
                            'Content-Type: application/json',
                        ];
                        $json_string = json_encode($vars);
                        //dd($json_string);
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
                } else if ($request->courier_id == 3) {
                    //steadfast courier entry
                    $credential = DB::table('stead_fast_apis')->select('is_active', 'api_key', 'secret_key')->where('id', 1)->first();
                    //dd($credential);
                    if ($credential->is_active == 1) {
                        $vars = [
                            'invoice' => $order_id->invoice_id ?? null,
                            'recipient_name' => $order_id->customer_name ?? null,
                            'recipient_address' => $order_id->customer_address ?? null,
                            'recipient_phone' => $order_id->customer_phone ?? null,
                            'cod_amount' => $order_id->due ?? 0,
                            'note' => '',
                        ];
                        $json_string = json_encode($vars);
                        $headers = [
                            'Api-Key: ' . $credential->api_key,
                            'Secret-Key: ' . $credential->secret_key,
                            'Content-Type: application/json',
                        ];
                        //dd($headers);
                        $curl = curl_init();

                        curl_setopt_array($curl, [
                            CURLOPT_URL => 'https://portal.steadfast.com.bd/api/v1/create_order',
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => $json_string,
                            CURLOPT_HTTPHEADER => $headers,
                        ]);
                        $data = curl_exec($curl);
                        curl_close($curl);

                        if (json_decode($data)->status != 200) {
                            $date = Carbon::now() . "\n";
                            $fp = fopen(base_path('storage/logs/stead_fast_entry_log.txt'), 'a'); //opens file in append mode
                            fwrite($fp, $date . json_encode($data) . "\n\n");
                            fclose($fp);
                        }

                        if (json_decode($data)->status == 200) {
                            $order_id->update([
                                'status' => $request->status,
                                'stead_fast_consignment_id' => json_decode($data)->status == 200 ? json_decode($data)->consignment->tracking_code : null,
                            ]);
                        }
                    }
                }*/
            } else {
                $order_id->update([
                    'status' => $request->status,
                ]);
            }

            $sms = SmsSetting::where('status', $order_id->status)->first();
            // send whatsapp
            if ($sms && $sms->is_whatsapp == 1 && $sms->template_name != null) {
                $this->WpServices->sendOrderWhatsapp($order_id, $sms->template_name, $sms->status);
            }

            if (Auth::guard('admin')->check()) {
                return to_route('admin.orders')->with('success', 'Order Updated Successfully');
            } elseif (Auth::guard('manager')->check()) {
                return to_route('manager.orders')->with('success', 'Order Updated Successfully');
            } elseif (Auth::guard('employee')->check()) {
                return to_route('employee.orders')->with('success', 'Order Updated Successfully');
            } else {
                return back()->with('warning', 'Something Went Wrong');
            }
        } else {
            return back()->with('error', 'Please Select A Product');
        }
    }

    public function statusChange($id, $status)
    {
        // dd(4);
        $web_settings = DB::table('web_settings')->where('id', 1)->first();
        $order_id = Order::with('get_products.get_product')->find($id);

        // if ($order_id->status != 5 && $status == 5) {
        //     //send order confirm sms
        //     if ($web_settings->is_order_confirm_sms == 1) {
        //         $products = '';
        //         foreach ($order_id->get_products as $key => $item) {
        //             if ($key != 0) {
        //                 $products .= "\n";
        //             }
        //             $products .= $item->get_product->name . '.';
        //         }
        //         $mgs_body = strtr($web_settings->order_confirm_sms, [
        //             '{$invoice_id}' => $order_id->invoice_id ?? null,
        //             '{$products}' => $products ?? null,
        //             '{$total_amount}' => $order_id->total ?? 0,
        //         ]);

        //         $apikey = config('app.sms_api_key');
        //         //$sender = config('app.sms_sender');

        //         $msisdn = ltrim(BanglaToEnglishConverter::bn2en($order_id->customer_phone), '+');
        //         //dd($apikey, $msisdn, $text);
        //         $curl = curl_init();

        //         curl_setopt_array($curl, [
        //             CURLOPT_URL => 'https://api.sms.net.bd/sendsms',
        //             CURLOPT_RETURNTRANSFER => true,
        //             CURLOPT_CUSTOMREQUEST => 'POST',
        //             CURLOPT_POSTFIELDS => ['api_key' => $apikey, 'msg' => $mgs_body, 'to' => $msisdn],
        //         ]);

        //         $response = curl_exec($curl);

        //         curl_close($curl);
        //         //dd($response);
        //     }

        //     /*if ($order_id->courier_id == 1) {
        //         //pathao courier entry
        //         $credential = DB::table('pathao_apis')->select('is_active', 'access_token', 'store_id')->where('id', 1)->first();
        //         if ($credential->is_active == 1) {
        //             $url = 'https://api-hermes.pathao.com/aladdin/api/v1/orders';
        //             $item_description = "";
        //             foreach ($order_id->get_products as $key => $get_product) {
        //                 $item_description .= $get_product->get_product->name . "\n";
        //             }
        //             $curl = curl_init();
        //             $vars = [
        //                 'store_id' => $credential->store_id,
        //                 'merchant_order_id' => $order_id->invoice_id ?? null,
        //                 'sender_name' => env('APP_NAME'),
        //                 //'sender_phone' => null,
        //                 'recipient_name' => $order_id->customer_name ?? null,
        //                 'recipient_phone' => $order_id->customer_phone ?? null,
        //                 'recipient_address' => $order_id->customer_address ?? null,
        //                 'recipient_city' => $order_id->courier_city_id ?? null,
        //                 'recipient_zone' => $order_id->courier_zone_id ?? null,
        //                 'recipient_area' => null,
        //                 'delivery_type' => 48,
        //                 'item_type' => 2,
        //                 'special_instruction' => null,
        //                 'item_quantity' => $order_id->get_products->sum('qty') ?? 1,
        //                 'item_weight' => 0.5,
        //                 'amount_to_collect' => $order_id->due ?? 0,
        //                 'item_description' => $item_description ?? null,
        //             ];
        //             $headers = [
        //                 'accept: application/json',
        //                 'content-type: application/json',
        //                 'authorization: Bearer ' . $credential->access_token,
        //             ];
        //             //dd($vars);
        //             $json_string = json_encode($vars);
        //             //dd($json_string);
        //             curl_setopt($curl, CURLOPT_URL, $url);
        //             curl_setopt($curl, CURLOPT_POST, true);
        //             curl_setopt($curl, CURLOPT_POSTFIELDS, $json_string);
        //             curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        //             curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        //             $data = curl_exec($curl);
        //             $data = json_decode($data, true);
        //             curl_close($curl);

        //             if ($data['code'] != 200) {
        //                 $date = Carbon::now() . "\n";
        //                 $fp = fopen(base_path('storage/logs/pathao_entry_log.txt'), 'a'); //opens file in append mode
        //                 fwrite($fp, $date . json_encode($data) . "\n\n");
        //                 fclose($fp);
        //             }
        //             //dd($data['data']->consignment_id);

        //             $order_id->update([
        //                 'status' => $status,
        //                 'pathao_consignment_id' => $data['code'] == 200 ? $data['data']['consignment_id'] : null,
        //             ]);
        //         } else {
        //             $order_id->update([
        //                 'status' => $status,
        //             ]);
        //         }
        //     } elseif ($order_id->courier_id == 2) {
        //         //redx courier entry
        //         $redx_credential = DB::table('redx_apis')->select('is_active', 'access_token')->where('id', 1)->first();
        //         if ($redx_credential->is_active == 1) {
        //             //get delivery_area
        //             $curl = curl_init();

        //             curl_setopt_array($curl, [
        //                 CURLOPT_URL => 'https://openapi.redx.com.bd/v1.0.0-beta/areas',
        //                 CURLOPT_RETURNTRANSFER => true,
        //                 CURLOPT_ENCODING => '',
        //                 CURLOPT_MAXREDIRS => 10,
        //                 CURLOPT_TIMEOUT => 0,
        //                 CURLOPT_FOLLOWLOCATION => true,
        //                 CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //                 CURLOPT_CUSTOMREQUEST => 'GET',
        //                 CURLOPT_HTTPHEADER => [
        //                     'API-ACCESS-TOKEN: Bearer ' . $redx_credential->access_token,
        //                 ],
        //             ]);
        //             $response = curl_exec($curl);
        //             curl_close($curl);
        //             $delivery_areas = '';
        //             foreach (json_decode($response, true)['areas'] as $delivery_area) {
        //                 if ($delivery_area['id'] == $order_id->courier_city_id) {
        //                     $delivery_areas = $delivery_area['name'];
        //                     break;
        //                 }
        //             }

        //             //store order into redx
        //             $url = 'https://openapi.redx.com.bd/v1.0.0-beta/parcel';
        //             $curl = curl_init();
        //             $vars = [
        //                 "customer_name" => $order_id->customer_name ?? null,
        //                 "customer_phone" => $order_id->customer_phone ?? null,
        //                 "delivery_area" => $delivery_areas ?? null,
        //                 "delivery_area_id" => $order_id->courier_city_id ?? null,
        //                 "customer_address" => $order_id->customer_address ?? null,
        //                 "merchant_invoice_id" => $order_id->invoice_id ?? null,
        //                 "cash_collection_amount" => $order_id->due ?? 0,
        //                 "parcel_weight" => 500,
        //                 "instruction" => "",
        //                 "value" => $order_id->due ?? 0,
        //             ];
        //             $headers = [
        //                 'API-ACCESS-TOKEN: Bearer ' . $redx_credential->access_token,
        //                 'Content-Type: application/json',
        //             ];
        //             $json_string = json_encode($vars);
        //             //dd($json_string);
        //             curl_setopt_array($curl, [
        //                 CURLOPT_HTTPHEADER => $headers,
        //                 CURLOPT_URL => $url,
        //                 CURLOPT_RETURNTRANSFER => true,
        //                 CURLOPT_ENCODING => '',
        //                 CURLOPT_MAXREDIRS => 10,
        //                 CURLOPT_TIMEOUT => 0,
        //                 CURLOPT_FOLLOWLOCATION => true,
        //                 CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //                 CURLOPT_CUSTOMREQUEST => 'POST',
        //                 CURLOPT_POSTFIELDS => $json_string,

        //             ]);
        //             $response = curl_exec($curl);
        //             curl_close($curl);

        //             $order_id->update([
        //                 'status' => $status,
        //                 'redx_tracking_id' => json_decode($response, true)['tracking_id'] ?? null,
        //             ]);
        //         } else {
        //             $order_id->update([
        //                 'status' => $status,
        //             ]);
        //         }
        //     } else if ($order_id->courier_id == 3) {
        //         //steadfast courier entry
        //         $credential = DB::table('stead_fast_apis')->select('is_active', 'api_key', 'secret_key')->where('id', 1)->first();
        //         //dd($credential);
        //         if ($credential->is_active == 1) {
        //             $vars = [
        //                 'invoice' => $order_id->invoice_id ?? null,
        //                 'recipient_name' => $order_id->customer_name ?? null,
        //                 'recipient_address' => $order_id->customer_address ?? null,
        //                 'recipient_phone' => $order_id->customer_phone ?? null,
        //                 'cod_amount' => $order_id->total ?? 0,
        //                 'note' => '',
        //             ];
        //             $json_string = json_encode($vars);
        //             $headers = [
        //                 'Api-Key: ' . $credential->api_key,
        //                 'Secret-Key: ' . $credential->secret_key,
        //                 'Content-Type: application/json',
        //             ];
        //             //dd($headers);
        //             $curl = curl_init();

        //             curl_setopt_array($curl, [
        //                 CURLOPT_URL => 'https://portal.steadfast.com.bd/api/v1/create_order',
        //                 CURLOPT_RETURNTRANSFER => true,
        //                 CURLOPT_ENCODING => '',
        //                 CURLOPT_MAXREDIRS => 10,
        //                 CURLOPT_TIMEOUT => 0,
        //                 CURLOPT_FOLLOWLOCATION => true,
        //                 CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //                 CURLOPT_CUSTOMREQUEST => 'POST',
        //                 CURLOPT_POSTFIELDS => $json_string,
        //                 CURLOPT_HTTPHEADER => $headers,
        //             ]);
        //             $data = curl_exec($curl);
        //             curl_close($curl);

        //             if (json_decode($data)->status != 200) {
        //                 $date = Carbon::now() . "\n";
        //                 $fp = fopen(base_path('storage/logs/stead_fast_entry_log.txt'), 'a'); //opens file in append mode
        //                 fwrite($fp, $date . json_encode($data) . "\n\n");
        //                 fclose($fp);
        //             }

        //             if (json_decode($data)->status == 200) {
        //                 $order_id->update([
        //                     'status' => $status,
        //                     'stead_fast_consignment_id' => json_decode($data)->status == 200 ? json_decode($data)->consignment->tracking_code : null,
        //                 ]);
        //             }
        //         }
        //     } else {
        //         $order_id->update([
        //             'status' => $status,
        //         ]);
        //     }*/
        // } else {
        //     $order_id->update([
        //         'status' => $status,
        //     ]);
        // }

        $sms = SmsSetting::where('status', $status)->first();

        if ($sms && $sms->is_active == 1) {
            $this->sendSmsToCustomer($order_id, $sms);
        }
        // send whatsapp
        if ($sms && $sms->is_whatsapp == 1 && $sms->template_name != null) {
            $this->WpServices->sendOrderWhatsapp($order_id, $sms->template_name, $sms->status);
        }
        $order_id->update([
            'status' => $status,
        ]);

        // create transaction
        $status_name = '';

        if ($status == 0) {
            $status_name = 'Hold';
        } elseif ($status == 1) {
            $status_name = 'Delivered';
        } elseif ($status == 2) {
            $status_name = 'Processing';
        } elseif ($status == 3) {
            $status_name = 'Pending Payment';
        } elseif ($status == 4) {
            $status_name = 'Cancelled';
        } elseif ($status == 5) {
            $status_name = 'Pending Invoice';
        } elseif ($status == 6) {
            $status_name = 'On Delivery';
        } elseif ($status == 7) {
            $status_name = 'Pending Return';
        } elseif ($status == 8) {
            $status_name = 'Courier';
        } elseif ($status == 9) {
            $status_name = 'No Response';
        } elseif ($status == 10) {
            $status_name = 'Invoiced';
        } elseif ($status == 11) {
            $status_name = 'Return';
        } elseif ($status == 12) {
            $status_name = 'Incomplete';
        } elseif ($status == 13) {
            $status_name = 'Confirmed';
        } elseif ($status == 14) {
            $status_name = 'Stock Out';
        } elseif ($status == 15) {
            $status_name = 'Partial Delivery';
        } elseif ($status == 16) {
            $status_name = 'Lost';
        }

        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            $created_by = 'admin';
        } elseif (Auth::guard('manager')->check()) {
            $user = Auth::guard('manager')->user();
            $created_by = 'manager';
        } elseif (Auth::guard('employee')->check()) {
            $user = Auth::guard('employee')->user();
            $created_by = 'employee';
        }

        order_transaction(
            'local',
            $id,
            strtr(config('transaction_texts.order_status_change'), [
                '{status}' => $status_name,
                '{user_name}' => $user->name,
                '{role}' => $created_by,
            ]),
            null,
            $created_by,
            $user->id,
            null
        );

        return back()->with('success', 'Order Status Changed Successfully');
    }

    public function paymentStatusChange($id, $status)
    {
        // dd($id,$status);
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

        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            $created_by = 'admin';
        } elseif (Auth::guard('manager')->check()) {
            $user = Auth::guard('manager')->user();
            $created_by = 'manager';
        } elseif (Auth::guard('employee')->check()) {
            $user = Auth::guard('employee')->user();
            $created_by = 'employee';
        }

        order_transaction(
            'local',
            $id,
            strtr(config('transaction_texts.order_payment_status_change'), [
                '{status}' => $status_name,
                '{user_name}' => $user->name,
                '{role}' => $created_by,
            ]),
            null,
            $created_by,
            $user->id,
            null
        );

        return back()->with('success', 'Order Payment Status Changed Successfully');
    }

    public function delete($id)
    {
        if (Auth::guard('admin')->check()) {
            $user_id = 'admin-'.Auth::guard('admin')->id();
        } elseif (Auth::guard('manager')->check()) {
            $user_id = 'manager-'.Auth::guard('manager')->id();
        } elseif (Auth::guard('employee')->check()) {
            $user_id = 'employee-'.Auth::guard('employee')->id();
        }
        $order = Order::find($id);

        if ($order->status == 1) {
            return back()->with('warning', 'Completed Order Can\'t Be Deleted!');
        } else {
            /*OrderProduct::where('order_id', $id)->delete();
            OrderAssign::where('order_id', $id)->delete();*/
            /*$order->update([
            'deleted_by' => $user_id
            ]);*/
            $order->delete();

            return back()->with('success', 'Order Deleted Successfully');
        }
    }

    public function ajaxGetProducts(Request $request)
    {
        // dd($request->all());
        $data = Product::with('get_thumb')->find($request->id);

        return view('backEnd.admin.orders.products', compact('data'))->render();
    }

    public function printInvoice(Request $request)
    {
        $data = Order::find($request->id);

        return view('backEnd.admin.orders.invoice2', compact('data'))->render();
    }

    public function printBulkInvoice(Request $request)
    {
        $data = Order::find($request->all_inv_id);

        return view('backEnd.admin.orders.bulk_invoice_2', compact('data'))->render();
    }

    public function printBulkLabelInvoice(Request $request)
    {
        // $data = Order::find([54]);
        // return view('backEnd.admin.orders.bulk_label_invoice', compact('data'));
        // $data = Order::find($request->all_inv_id);
        $d = Order::join('order_products', 'order_products.order_id', 'orders.id')
            ->join('products', 'products.id', 'order_products.product_id')
            ->orderBy('products.name', 'asc')
            ->whereIn('orders.id', $request->all_inv_id)->pluck('orders.id')->toArray();
        $ids_ordered = implode(',', $d);
        $data = Order::orderByRaw("FIELD(id, $ids_ordered)")
            ->find($d);

        // dd($data);
        // return view('backEnd.admin.orders.bulk_label_invoice', compact('data'));
        return view('backEnd.admin.orders.bulk_label_invoice', compact('data'))->render();
    }

    public function allStatusChange(Request $request)
    {
        // dd(explode(',',$request->all_status));
        foreach (explode(',', $request->all_status) as $item) {
            $web_settings = DB::table('web_settings')->where('id', 1)->first();
            $order_id = Order::with('get_products.get_product')->find($item);
            // send sms
            $sms = SmsSetting::where('status', $request->status)->first();
            if ($sms && $sms->is_active == 1) {
                $this->sendSmsToCustomer($order_id, $sms);
            }

            if ($sms && $sms->is_whatsapp == 1 && $sms->template_name != null) {
                $this->WpServices->sendOrderWhatsapp($order_id, $sms->template_name, $sms->status);
            }

            if ($order_id->status != 5 && $request->status == 5) {

                /*if ($order_id->courier_id == 1) {
                    //pathao courier entry
                    $credential = DB::table('pathao_apis')->select('is_active', 'access_token', 'store_id')->where('id', 1)->first();
                    if ($credential->is_active == 1) {
                        $url = 'https://api-hermes.pathao.com/aladdin/api/v1/orders';
                        $item_description = "";
                        foreach ($order_id->get_products as $key => $get_product) {
                            $item_description .= $get_product->get_product->name . "\n";
                        }
                        $curl = curl_init();
                        $vars = [
                            'store_id' => $credential->store_id,
                            'merchant_order_id' => $order_id->invoice_id ?? null,
                            'sender_name' => env('APP_NAME'),
                            //'sender_phone' => null,
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
                            'authorization: Bearer ' . $credential->access_token,
                        ];
                        //dd($vars);
                        $json_string = json_encode($vars);
                        //dd($json_string);
                        curl_setopt($curl, CURLOPT_URL, $url);
                        curl_setopt($curl, CURLOPT_POST, true);
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $json_string);
                        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        $data = curl_exec($curl);
                        $data = json_decode($data, true);
                        curl_close($curl);
                        if ($data['code'] != 200) {
                            $date = Carbon::now() . "\n";
                            $fp = fopen(base_path('storage/logs/pathao_entry_log.txt'), 'a'); //opens file in append mode
                            fwrite($fp, $date . json_encode($data) . "\n\n");
                            fclose($fp);
                        }
                        //dd($data['data']->consignment_id);

                        $order_id->update([
                            'status' => $request->status,
                            'pathao_consignment_id' => $data['code'] == 200 ? $data['data']['consignment_id'] : null,
                        ]);
                    } else {
                        $order_id->update([
                            'status' => $request->status,
                        ]);
                    }
                }*/

                if ($order_id->courier_id == 2) {
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
                    } else {
                        $order_id->update([
                            'status' => $request->status,
                        ]);
                    }
                } elseif ($order_id->courier_id == 3) {
                    // steadfast courier entry
                    $credential = DB::table('stead_fast_apis')->select('is_active', 'api_key', 'secret_key')->where('id', 1)->first();
                    // dd($credential);
                    if ($credential->is_active == 1) {
                        $vars = [
                            'invoice' => $order_id->invoice_id ?? null,
                            'recipient_name' => $order_id->customer_name ?? null,
                            'recipient_address' => $order_id->customer_address ?? null,
                            'recipient_phone' => $order_id->customer_phone ?? null,
                            'cod_amount' => $order_id->total ?? 0,
                            'note' => '',
                        ];
                        $json_string = json_encode($vars);
                        $headers = [
                            'Api-Key: '.$credential->api_key,
                            'Secret-Key: '.$credential->secret_key,
                            'Content-Type: application/json',
                        ];
                        // dd($headers);
                        $curl = curl_init();

                        curl_setopt_array($curl, [
                            CURLOPT_URL => 'https://portal.steadfast.com.bd/api/v1/create_order',
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => $json_string,
                            CURLOPT_HTTPHEADER => $headers,
                        ]);
                        $data = curl_exec($curl);
                        curl_close($curl);

                        if (json_decode($data)->status != 200) {
                            $date = \Illuminate\Support\Facades\Date::now()."\n";
                            $fp = fopen(base_path('storage/logs/stead_fast_entry_log.txt'), 'a'); // opens file in append mode
                            fwrite($fp, $date.json_encode($data)."\n\n");
                            fclose($fp);
                        }

                        if (json_decode($data)->status == 200) {
                            $order_id->update([
                                'status' => $request->status,
                                'stead_fast_consignment_id' => json_decode($data)->status == 200 ? json_decode($data)->consignment->tracking_code : null,
                            ]);
                        }
                    }
                } else {
                    $order_id->update([
                        'status' => $request->status,
                    ]);
                }
            } else {
                $order_id->update([
                    'status' => $request->status,
                ]);
            }
            //                }
            /*} else {
            return back()->with('error', 'Something Went wrong');
            }*/

            // create transaction
            $status_name = '';
            if ($request->status == 0) {
                $status_name = 'Hold';
            } elseif ($request->status == 1) {
                $status_name = 'Delivered';
            } elseif ($request->status == 2) {
                $status_name = 'Processing';
            } elseif ($request->status == 3) {
                $status_name = 'Pending Payment';
            } elseif ($request->status == 4) {
                $status_name = 'Cancelled';
            } elseif ($request->status == 5) {
                $status_name = 'Pending Invoice';
            } elseif ($request->status == 6) {
                $status_name = 'On Delivery';
            } elseif ($request->status == 7) {
                $status_name = 'Pending Return';
            } elseif ($request->status == 8) {
                $status_name = 'Courier';
            } elseif ($request->status == 9) {
                $status_name = 'No Response';
            } elseif ($request->status == 10) {
                $status_name = 'Invoiced';
            } elseif ($request->status == 11) {
                $status_name = 'Return';
            } elseif ($request->status == 12) {
                $status_name = 'Incomplete';
            } elseif ($request->status == 13) {
                $status_name = 'Confirmed';
            } elseif ($request->status == 14) {
                $status_name = 'Stock Out';
            } elseif ($request->status == 15) {
                $status_name = 'Partial Delivery';
            } elseif ($request->status == 16) {
                $status_name = 'Lost';
            }

            if (Auth::guard('admin')->check()) {
                $user = Auth::guard('admin')->user();
                $created_by = 'admin';
            } elseif (Auth::guard('manager')->check()) {
                $user = Auth::guard('manager')->user();
                $created_by = 'manager';
            } elseif (Auth::guard('employee')->check()) {
                $user = Auth::guard('employee')->user();
                $created_by = 'employee';
            }

            order_transaction(
                'local',
                $item,
                strtr(config('transaction_texts.order_status_change'), [
                    '{status}' => $status_name,
                    '{user_name}' => $user->name,
                    '{role}' => $created_by,
                ]),
                null,
                $created_by,
                $user->id,
                null
            );
        }

        return back()->with('success', 'Order Status Changed Successfully');
    }

    public function bulkDelete(Request $request)
    {
        // dd($request->all_id);
        foreach (explode(',', $request->all_id) as $item) {
            /*OrderProduct::where('order_id', $item)->delete();
            OrderAssign::where('order_id', $item)->delete();*/
            Order::find($item)->delete();
        }

        // dd($request->all());
        return back()->with('success', 'Deleted Successfully');
    }

    public function bulkAssign(Request $request)
    {
        // dd($request->all());
        foreach (explode(',', $request->all_order_id) as $item) {
            $check = OrderAssign::where('order_id', $item)->first();
            if ($check) {
                OrderAssign::where('order_id', $item)->update([
                    'employee_id' => $request->employee_id,
                ]);
            } else {
                OrderAssign::create([
                    'order_id' => $item,
                    'employee_id' => $request->employee_id,
                ]);
            }

            $emp = DB::table('employees')->select('name')->where('id', $request->employee_id)->first();
            // create transaction
            if (Auth::guard('admin')->check()) {
                $user = Auth::guard('admin')->user();
                $created_by = 'admin';
            } elseif (Auth::guard('manager')->check()) {
                $user = Auth::guard('manager')->user();
                $created_by = 'manager';
            } elseif (Auth::guard('employee')->check()) {
                $user = Auth::guard('employee')->user();
                $created_by = 'employee';
            }

            order_transaction(
                'local',
                $item,
                strtr(config('transaction_texts.order_assign'), [
                    '{employee_name}' => $emp->name,
                    '{user_name}' => $user->name,
                    '{role}' => $created_by,
                ]),
                null,
                $created_by,
                $user->id,
                $request->employee_id
            );
        }

        return back()->with('success', 'Assigned Successfully');
    }

    public function bulkEqualAssign(Request $request)
    {
        $active_employees = Employee::where('status', 1)->where('start_time', '<=', \Illuminate\Support\Facades\Date::now()->toTimeString())->where('end_time', '>=', \Illuminate\Support\Facades\Date::now()->toTimeString())->get();

        $total_orders = Order::select('id')->find(explode(',', $request->eq_assign_order_ids));
        $per_emp_orders = round(count($total_orders) / count($active_employees));

        // dd($total_orders->take(50));

        $skip = 0;
        foreach ($active_employees as $active_employee) {
            foreach ($total_orders->skip($skip)->take($per_emp_orders) as $total_order) {
                $check = OrderAssign::where('order_id', $total_order->id)->first();
                if ($check) {
                    OrderAssign::where('order_id', $total_order->id)->update([
                        'employee_id' => $active_employee->id,
                    ]);
                } else {
                    OrderAssign::create([
                        'order_id' => $total_order->id,
                        'employee_id' => $active_employee->id,
                    ]);
                }

                // create transaction
                if (Auth::guard('admin')->check()) {
                    $user = Auth::guard('admin')->user();
                    $created_by = 'admin';
                } elseif (Auth::guard('manager')->check()) {
                    $user = Auth::guard('manager')->user();
                    $created_by = 'manager';
                } elseif (Auth::guard('employee')->check()) {
                    $user = Auth::guard('employee')->user();
                    $created_by = 'employee';
                }

                order_transaction(
                    'local',
                    $total_order->id,
                    strtr(config('transaction_texts.order_assign'), [
                        '{employee_name}' => $active_employee->name,
                        '{user_name}' => $user->name,
                        '{role}' => $created_by,
                    ]),
                    null,
                    $created_by,
                    $user->id,
                    $request->employee_id
                );
            }
            $skip += $per_emp_orders;
        }

        return back()->with('success', 'Equal Assign Completed');
    }

    public function singleAssign(Request $request)
    {
        // dd($request->all());
        $check = OrderAssign::where('order_id', $request->order_id)->first();
        if ($check) {
            OrderAssign::where('order_id', $request->order_id)->update([
                'employee_id' => $request->employee_id,
            ]);
        } else {
            OrderAssign::create([
                'order_id' => $request->order_id,
                'employee_id' => $request->employee_id,
            ]);
        }

        $emp = DB::table('employees')->select('name')->where('id', $request->employee_id)->first();
        // create transaction
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            $created_by = 'admin';
        } elseif (Auth::guard('manager')->check()) {
            $user = Auth::guard('manager')->user();
            $created_by = 'manager';
        } elseif (Auth::guard('employee')->check()) {
            $user = Auth::guard('employee')->user();
            $created_by = 'employee';
        }

        order_transaction(
            'local',
            $request->order_id,
            strtr(config('transaction_texts.order_assign'), [
                '{employee_name}' => $emp->name,
                '{user_name}' => $user->name,
                '{role}' => $created_by,
            ]),
            null,
            $created_by,
            $user->id,
            $request->employee_id
        );

        return back()->with('success', 'Assigned Successfully');
    }

    public function courierCsv(Request $request)
    {

        if ($request->courier_csv == 1) {
            $name = 'pathao';
            $file_name = $name.'_'.date('d-M-Y').'.csv';
        } elseif ($request->courier_csv == 2) {
            $name = 'redex';
            $file_name = $name.'_'.date('d-M-Y').'.xlsx';
        } elseif ($request->courier_csv == 3) {
            $name = 'paperfly';
            $file_name = $name.'_'.date('d-M-Y').'.xlsx';
        } elseif ($request->courier_csv == 4) {
            $name = 'stead_fast';
            $file_name = $name.'_'.date('d-M-Y').'.xlsx';
        } elseif ($request->courier_csv == 0) {
            $name = 'export_orders';
            $file_name = $name.'_'.date('d-M-Y').'.xlsx';
        } else {
            return back()->with('error', 'Something Went Wrong');
        }

        return Excel::download(new OrderExport(explode(',', $request->all_ord_id), $request->courier_csv), $file_name);
    }

    public function transactionView(Request $request)
    {
        $transactions = OrderTransaction::select('type', 'text', 'created_at')->where('order_id', $request->id)->orderBy('id', 'desc')->get();

        // dd($transactions);
        return view('backEnd.admin.orders.transactions', compact('transactions'))->render();
    }

    public function noteUpdate(Request $request)
    {
        // dd($request->all());
        if ($request->has('courier_note')) {
            $input = [
                'courier_note' => $request->courier_note,
            ];
        } elseif ($request->has('staff_note')) {

            $input = [
                'staff_note' => $request->staff_note,
            ];
        }

        NoteHistory::create([
            'order_id' => $request->id,
            'user_id' => Auth::guard('admin')->check() ? Auth::guard('admin')->id() : (Auth::guard('manager')->check() ? Auth::guard('manager')->id() : (Auth::guard('employee')->check() ? Auth::guard('employee')->id() : null)),
            'user_type' => Auth::guard('admin')->check() ? 'admin' : (Auth::guard('manager')->check() ? 'manager' : (Auth::guard('employee')->check() ? 'employee' : null)),
            'text' => $request->courier_note ?? $request->staff_note,
        ]);

        Order::find($request->id)->update($input);

        return back()->with('success', 'Note Updated Successfully');
    }

    public function steadFastOrderSync()
    {
        $orders = Order::where([['status', 5], ['stead_fast_consignment_id', '!=', null]])->get();
        // dd($orders);
        $credential = DB::table('stead_fast_apis')->select('is_active', 'api_key', 'secret_key')->where('id', 1)->first();
        $headers = [
            'Api-Key: '.$credential->api_key,
            'Secret-Key: '.$credential->secret_key,
            'Content-Type: application/json',
        ];

        foreach ($orders as $order) {
            if ($credential->is_active == 1) {
                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => 'https://portal.steadfast.com.bd/api/v1/status_by_trackingcode/'.$order->stead_fast_consignment_id,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => $headers,
                ]);
                $data = curl_exec($curl);
                curl_close($curl);

                if (json_decode($data)->status != 200) {
                    $date = \Illuminate\Support\Facades\Date::now()."\n";
                    $fp = fopen(base_path('storage/logs/stead_fast_sync_log.txt'), 'a'); // opens file in append mode
                    fwrite($fp, $date.json_encode($data)."\n\n");
                    fclose($fp);
                }

                if (json_decode($data)->status == 200) {
                    if (json_decode($data)->delivery_status == 'delivered') {
                        $order->update([
                            'status' => 1,
                        ]);
                    } elseif (json_decode($data)->delivery_status == 'cancelled') {
                        $order->update([
                            'status' => 7,
                        ]);
                    }
                }
            }
        }

        return back()->with('success', 'SteadFast Status Updated successfully');
    }

    public function sendToCourier(Request $request)
    {
        // dd($request->all());
        if ($request->send_to_courier == 1) { // 1= pathao

            $credential = DB::table('pathao_apis')->select('is_active', 'access_token', 'store_id')->where('id', 1)->first();
            if ($credential->is_active == 1) {
                foreach (explode(',', $request->all_status) as $item) {
                    $order_id = Order::with('get_products')->where('pathao_consignment_id', null)->find($item);
                    if ($order_id) {
                        // dd($order_id);
                        $vars[$order_id->id] = [
                            'store_id' => $credential->store_id,
                            'merchant_order_id' => $order_id->invoice_id ?? null,
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
                    }
                }
                $orders['orders'] = $vars;
                $json_string = json_encode($orders);

                // dd($json_string);
                $url = 'https://api-hermes.pathao.com/aladdin/api/v1/orders/bulk';
                $curl = curl_init();
                $headers = [
                    'accept: application/json',
                    'content-type: application/json',
                    'authorization: Bearer '.$credential->access_token,
                ];

                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $json_string);
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $data = curl_exec($curl);
                $data = json_decode($data, true);
                curl_close($curl);

                // dd($data['errors']);
                if ($data['code'] != 202 && $data['code'] == 422) {
                    foreach ($data['errors'] as $key1 => $item1) {
                        $order_id = Order::select('id', 'courier_api_response')->find(explode('.', (string) $key1)[1]);
                        if ($order_id) {
                            $api_response = date('d-m-y h:i:s A').' -> '.str_replace($key1, explode('.', (string) $key1)[2], $item1[0]);
                            $order_id->update([
                                'courier_api_response' => $order_id->courier_api_response.$api_response."\n\n",
                            ]);
                        }
                    }
                } else {
                    $date = \Illuminate\Support\Facades\Date::now()."\n";
                    $fp = fopen(base_path('storage/logs/pathao_entry_log.txt'), 'a'); // opens file in append mode
                    fwrite($fp, $date.json_encode($data)."\n\n");
                    fclose($fp);
                }

                return back()->with('success', 'Selected orders send to pathao courier');
            } else {
                return back()->with('error', 'Pathao Courier API is not active');
            }
        } elseif ($request->send_to_courier == 4) { // 4= carrybee

            $credential = DB::table('carry_bee_apis')->select('is_active', 'access_token', 'store_id')->where('id', 1)->first();
            if ($credential->is_active == 1) {
                // bulk entry part start
                /*foreach (explode(',', $request->all_status) as $item) {
                    $order_id = Order::with('get_products')->where('carrybee_consignment_id', null)->find($item);
                    if ($order_id) {
                        //dd($order_id);
                        $vars[$order_id->id] = [
                            'store_id' => $credential->store_id,
                            'Merchant_id' => '',
                            'merchant_order_id' => $order_id->invoice_id ?? null,
                            'recipient_name' => $order_id->customer_name ?? null,
                            'recipient_phone' => $order_id->customer_phone ?? null,
                            'recipient_address' => $order_id->customer_address ?? null,
                            'city_id' => $order_id->courier_city_id ?? null,
                            'zone_id' => $order_id->courier_zone_id ?? null,
                            'area_id' => null,
                            'delivery_type' => 48,
                            'product_type' => 2,
                            'special_instruction' => null,
                            'quantity' => $order_id->get_products->sum('qty') ?? 1,
                            'weight' => 0.5,
                            'amount_collect' => $order_id->total ?? 0,
                            'item_desc' => $item_description ?? null,
                        ];
                    }
                }
                $orders['orders'] = $vars;
                $json_string = json_encode($orders);

                //dd($json_string);
                $url = 'https://developers.carrybee.com/api/orders/bulk';
                $curl = curl_init();
                $headers = [
                    'accept: application/json',
                    'content-type: application/json',
                    'authorization: Bearer ' . $credential->access_token,
                ];

                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $json_string);
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $data = curl_exec($curl);
                $data = json_decode($data, true);
                curl_close($curl);

                //dd($data);
                if ($data['success'] == false) {
                    foreach ($data['reasons'] as $key1 => $item1) {
                        $order_id = Order::select('id', 'courier_api_response')->find(explode('.', $key1)[1]);
                        if ($order_id) {
                            $api_response = date('d-m-y h:i:s A') . " -> " . str_replace($key1, explode('.', $key1)[2], $item1[0]);
                            $order_id->update([
                                'courier_api_response' => $order_id->courier_api_response . $api_response . "\n\n",
                            ]);
                        }
                    }
                } else {
                    $date = Carbon::now() . "\n";
                    $fp = fopen(base_path('storage/logs/carrybee_entry_log.txt'), 'a'); //opens file in append mode
                    fwrite($fp, $date . json_encode($data) . "\n\n");
                    fclose($fp);
                }*/
                // bulk entry part end

                // single entry part start
                foreach (explode(',', $request->all_status) as $item) {
                    $order_id = Order::with('get_products')->where([['carrybee_consignment_id', null], ['courier_id', 4]])->find($item);
                    if ($order_id) {
                        $item_description = '';
                        foreach ($order_id->get_products as $get_product) {
                            $item_description .= $get_product->get_product->name."\n";
                        }
                        // dd($order_id);
                        $vars = [
                            'store_id' => $credential->store_id,
                            'Merchant_id' => '',
                            'merchant_order_id' => $order_id->invoice_id ?? null,
                            'recipient_name' => $order_id->customer_name ?? null,
                            'recipient_phone' => $order_id->customer_phone ?? null,
                            'recipient_address' => $order_id->customer_address ?? null,
                            'city_id' => $order_id->courier_city_id ?? null,
                            'zone_id' => $order_id->courier_zone_id ?? null,
                            'area_id' => null,
                            'delivery_type' => 48,
                            'product_type' => 2,
                            'special_instruction' => null,
                            'quantity' => $order_id->get_products->sum('qty') ?? 1,
                            'weight' => 0.5,
                            'amount_collect' => $order_id->total ?? 0,
                            'item_desc' => $item_description ?? null,
                        ];

                        $json_string = json_encode($vars);

                        // dd($json_string);
                        $url = 'https://developers.carrybee.com/api/orders';
                        $curl = curl_init();
                        $headers = [
                            'accept: application/json',
                            'content-type: application/json',
                            'authorization: Bearer '.$credential->access_token,
                        ];

                        curl_setopt($curl, CURLOPT_URL, $url);
                        curl_setopt($curl, CURLOPT_POST, true);
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $json_string);
                        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        $data = curl_exec($curl);
                        $data = json_decode($data);
                        curl_close($curl);

                        if ($data->success == true) {
                            $order_id->update([
                                'carrybee_consignment_id' => $data->data ? $data->data->consignment_id : null,
                            ]);
                        } else {
                            $date = \Illuminate\Support\Facades\Date::now()."\n";
                            $fp = fopen(base_path('storage/logs/carrybee_entry_log.txt'), 'a'); // opens file in append mode
                            fwrite($fp, $date.json_encode($data->errors, JSON_PRETTY_PRINT)."\n\n");
                            fclose($fp);
                        }
                    }
                }

                // single entry part end
                return back()->with('success', 'Selected orders send to Carrybee courier');
            } else {
                return back()->with('error', 'Carrybee Courier API is not active');
            }
        }
    }

    // getShipping
    public function getShipping(Request $request)
    {
        $shipping = ShippingMethod::where('id', $request->id)->first();

        return response()->json($shipping);
    }

    private function sendSmsToCustomer($order_id, $sms)
    {
        // dd($order_id, $sms);
        $products = '';
        foreach ($order_id->get_products as $key => $item) {
            if ($key != 0) {
                $products .= "\n";
            }
            $products .= $item->get_product->name.'.';
        }
        $mgs_body = strtr($sms->message, [
            '{$invoice_id}' => $order_id->invoice_id ?? null,
            '{$products}' => $products ?? null,
            '{$total_amount}' => $order_id->total ?? 0,
        ]);
        // dd($mgs_body);

        $apikey = config('app.sms_api_key');
        // $sender = config('app.sms_sender');

        $msisdn = ltrim((string) BanglaToEnglishConverter::bn2en($order_id->customer_phone), '+');
        // dd($apikey, $msisdn, $text);
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.sms.net.bd/sendsms',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => ['api_key' => $apikey, 'msg' => $mgs_body, 'to' => $msisdn],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);
        // dd($response);
    }
    // private function sendWhatsappToCustomer($order_id, $sms)
    // {
    //     $products = $order_id->get_products->map(function ($item) {
    //         return  $item->qty . ' x ' . $item->get_product->name;
    //     })->implode(', ');
    //     // dd($products);
    //     $api_settings = WebSettings::where('id', 1)->first();
    //     $phone_id = $api_settings->wp_phone_number_id;
    //     $token = $api_settings->wp_access_token;
    //     $to =  $order_id->customer_phone;
    //     // Clean and format phone number
    //     $to = preg_replace('/[^\d]/', '', $to);
    //     if (!str_starts_with($to, '880')) {
    //         $to = '880' . ltrim($to, '0');
    //     }
    //     // dd($to);
    //     $template_name = "order_confirm";
    //     $language_code = "bn";
    //     $invoice_number = $order_id->invoice_id;
    //     $product_list = $products;
    //     $total_price = $order_id->total;
    //     $curl = curl_init();
    //     $data = [
    //         "messaging_product" => "whatsapp",
    //         "to" => $to,
    //         "type" => "template",
    //         "template" => [
    //             "name" => $template_name,
    //             "language" => [
    //                 "code" => $language_code
    //             ],
    //             "components" => [
    //                 [
    //                     "type" => "body",
    //                     "parameters" => [
    //                         ["type" => "text", "text" => $invoice_number],
    //                         ["type" => "text", "text" => $product_list],
    //                         ["type" => "text", "text" => $total_price],
    //                     ],
    //                 ],
    //             ],
    //         ],
    //     ];

    //     curl_setopt_array($curl, array(
    //         CURLOPT_URL => "https://graph.facebook.com/v24.0/{$phone_id}/messages",
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_ENCODING => "",
    //         CURLOPT_MAXREDIRS => 10,
    //         CURLOPT_TIMEOUT => 30,
    //         CURLOPT_FOLLOWLOCATION => true,
    //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //         CURLOPT_CUSTOMREQUEST => "POST",
    //         CURLOPT_POSTFIELDS => json_encode($data, JSON_UNESCAPED_UNICODE),
    //         CURLOPT_HTTPHEADER => array(
    //             "Content-Type: application/json",
    //             "Authorization: Bearer " . $token
    //         ),
    //     ));
    //     $response = curl_exec($curl);
    //     dd($response);
    //     if (curl_errno($curl)) {
    //         echo "cURL Error: " . curl_error($curl);
    //     } else {
    //         $decoded = json_decode($response, true);
    //         if (isset($decoded['messages'])) {
    //             echo "✅ WhatsApp Template Message Sent Successfully!";
    //         } else {
    //             echo "❌ Failed to Send Message. Check Error Response Above.";
    //         }
    //     }
    //     curl_close($curl);
    // }
}
