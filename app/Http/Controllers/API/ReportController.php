<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Order;
use App\Models\OrderAssign;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function employeeOrders(Request $request)
    {
        $access_token = DB::table('web_settings')->select('api_access_token')->where('id', 1)->first()->api_access_token;
        abort_if($access_token != $request->bearerToken(), 403, 'Unauthorized Access');

        if ($request->input('paginate') != null) {
            $paginate = $request->input('paginate');
        } else {
            $paginate = 10;
        }
        $query = $request->input('query') ?? null;
        $custom_range = $request->input('custom_range');
        // dd($request->input('status'));
        $emp_id = $request->input('emp_id');
        $org_emp_id = Employee::where('p_id', $emp_id)->first();
        // return $org_emp_id;
        if ($org_emp_id) {
            $emp_id = $org_emp_id->id;
        }
        $status = $request->input('status');
        if ($request->input('emp_id')) {
            $data['total_order'] = OrderAssign::query();
            $data['total_hold_order'] = OrderAssign::query();
            $data['total_deliver_order'] = OrderAssign::query();
            $data['total_process_order'] = OrderAssign::query();
            $data['total_pend_pay_order'] = OrderAssign::query();
            $data['total_cancel_order'] = OrderAssign::query();
            $data['total_pending_delivery_order'] = OrderAssign::query();
            $data['total_on_delivery_order'] = OrderAssign::query();
            $data['total_return_order'] = OrderAssign::query();
            $data['total_courier_hold_order'] = OrderAssign::query();
            $data['total_nr_1_order'] = OrderAssign::query();
            $data['total_nr_2_order'] = OrderAssign::query();
            $data['orders'] = Order::query();

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

                $data['orders']->whereDate('created_at', $custom_range);
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

                $data['orders']->whereDate('created_at', $custom_range);
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

                $data['orders']->where('created_at', '>=', $custom_range);
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

                $data['orders']->whereMonth('created_at', $custom_range);
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

                $data['orders']->whereBetween('created_at', [$sd, $ed]);
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

                $data['orders']->whereBetween('created_at', [$sd, $ed]);
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

                $data['orders']->whereBetween('created_at', [$sd, $ed]);
            }

            $data['total_order'] = $data['total_order']->where('order_assigns.employee_id', $emp_id);
            $data['total_hold_order'] = $data['total_hold_order']->where([['order_assigns.employee_id', $emp_id], ['status', 0]]);
            $data['total_deliver_order'] = $data['total_deliver_order']->where([['order_assigns.employee_id', $emp_id], ['status', 1]]);
            $data['total_process_order'] = $data['total_process_order']->where([['order_assigns.employee_id', $emp_id], ['status', 2]]);
            $data['total_pend_pay_order'] = $data['total_pend_pay_order']->where([['order_assigns.employee_id', $emp_id], ['status', 3]]);
            $data['total_cancel_order'] = $data['total_cancel_order']->where([['order_assigns.employee_id', $emp_id], ['status', 4]]);
            $data['total_pending_delivery_order'] = $data['total_pending_delivery_order']->where([['order_assigns.employee_id', $emp_id], ['status', 5]]);
            $data['total_on_delivery_order'] = $data['total_on_delivery_order']->where([['order_assigns.employee_id', $emp_id], ['status', 6]]);
            $data['total_return_order'] = $data['total_return_order']->where([['order_assigns.employee_id', $emp_id], ['status', 7]]);
            $data['total_courier_hold_order'] = $data['total_courier_hold_order']->where([['order_assigns.employee_id', $emp_id], ['status', 8]]);
            $data['total_nr_1_order'] = $data['total_nr_1_order']->where([['order_assigns.employee_id', $emp_id], ['status', 9]]);
            $data['total_nr_2_order'] = $data['total_nr_2_order']->where([['order_assigns.employee_id', $emp_id], ['status', 10]]);

            $data['orders'] = $data['orders']->with('get_products', 'get_courier', 'get_assigned')->whereHas('get_assigned', function ($qry) use ($emp_id): void {
                $qry->where('employee_id', $emp_id);
            });

            if ($request->input('status') != null) {
                $data['orders'] = $data['orders']->where('status', $request->input('status'));
            }

            if ($request->input('query')) {
                $data['orders']->where('customer_phone', 'LIKE', "%{$request->input('query')}%");
                $data['orders']->orWhere('customer_name', 'LIKE', "%{$request->input('query')}%");
                $data['orders']->orWhere('invoice_id', 'LIKE', "%{$request->input('query')}%");

                $data['orders']->orWhereHas('get_products', function ($p) use ($query): void {
                    $p->join('products', 'products.id', 'order_products.product_id')->where('name', 'LIKE', "%{$query}%");
                });
            }

            $data['total_order'] = $data['total_order']->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->count();
            $data['total_hold_order'] = $data['total_hold_order']->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->count();
            $data['total_deliver_order'] = $data['total_deliver_order']->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->count();
            $data['total_process_order'] = $data['total_process_order']->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->count();
            $data['total_pend_pay_order'] = $data['total_pend_pay_order']->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->count();
            $data['total_cancel_order'] = $data['total_cancel_order']->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->count();
            $data['total_pending_delivery_order'] = $data['total_pending_delivery_order']->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->count();
            $data['total_on_delivery_order'] = $data['total_on_delivery_order']->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->count();
            $data['total_return_order'] = $data['total_return_order']->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->count();
            $data['total_courier_hold_order'] = $data['total_courier_hold_order']->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->count();
            $data['total_nr_1_order'] = $data['total_nr_1_order']->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->count();
            $data['total_nr_2_order'] = $data['total_nr_2_order']->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->count();
            // dd($data['total_order']);

            $data['count'] = $data['orders']->get()->count();
            $data['orders'] = $data['orders']->with('get_products.get_product', 'get_courier', 'get_assigned.get_employee')
                ->select('invoice_id', 'customer_name', 'customer_phone', 'customer_address', 'total', 'order_date', 'created_at', 'status', 'staff_note', 'courier_note', 'courier_status', 'id', 'ip_address', 'courier_id', 'paid', 'due', 'pathao_consignment_id', 'redx_tracking_id', 'payment_status')
                ->orderBy('id', 'desc')->paginate($paginate);
            foreach ($data['orders'] as $key => $order) {
                $duplicate_orders = DB::table('orders')->where('customer_phone', $order->customer_phone)->count();
                $data['orders'][$key]->duplicate_orders = $duplicate_orders;
            }
            $data['orders']->appends(['paginate' => $paginate, 'emp_id' => $emp_id]);
        } else {
            $data['total_order'] = 0;
            $data['total_hold_order'] = 0;
            $data['total_deliver_order'] = 0;
            $data['total_process_order'] = 0;
            $data['total_pend_pay_order'] = 0;
            $data['total_cancel_order'] = 0;
            $data['total_pending_delivery_order'] = 0;
            $data['total_on_delivery_order'] = 0;
            $data['total_return_order'] = 0;
            $data['total_courier_hold_order'] = 0;
            $data['total_nr_1_order'] = 0;
            $data['total_nr_2_order'] = 0;
            $data['orders'] = [];
        }

        // dd($data['orders']);
        return response()->json($data);
    }

    public function orderStatusProduct(Request $request)
    {
        $access_token = DB::table('web_settings')->select('api_access_token')->where('id', 1)->first()->api_access_token;
        abort_if($access_token != $request->bearerToken(), 403, 'Unauthorized Access');

        $products = DB::table('order_products')
            ->select('order_products.product_id')
            ->groupBy('order_products.product_id')
            ->pluck('product_id')->toArray();

        foreach ($products as $product) {
            $total_order[$product] = DB::table('order_products')
                ->join('orders', 'order_products.order_id', 'orders.id')
                ->where('order_products.product_id', $product)
                ->count();
        }
        arsort($total_order);
        $total_order = array_keys($total_order);

        foreach ($total_order as $product) {
            $prod_name = DB::table('products')->where('id', $product)->first()->name;
            $total_process_order = DB::table('order_products')
                ->join('orders', 'order_products.order_id', 'orders.id')
                ->where([['order_products.product_id', $product], ['orders.status', 2]])
                ->count();
            $total_nr_1_order = DB::table('order_products')
                ->join('orders', 'order_products.order_id', 'orders.id')
                ->where([['order_products.product_id', $product], ['orders.status', 9]])
                ->count();
            $total_nr_2_order = DB::table('order_products')
                ->join('orders', 'order_products.order_id', 'orders.id')
                ->where([['order_products.product_id', $product], ['orders.status', 10]])
                ->count();
            $total_hold_order = DB::table('order_products')
                ->join('orders', 'order_products.order_id', 'orders.id')
                ->where([['order_products.product_id', $product], ['orders.status', 0]])
                ->count();
            $total_cancel_order = DB::table('order_products')
                ->join('orders', 'order_products.order_id', 'orders.id')
                ->where([['order_products.product_id', $product], ['orders.status', 4]])
                ->count();
            $total_pend_pay_order = DB::table('order_products')
                ->join('orders', 'order_products.order_id', 'orders.id')
                ->where([['order_products.product_id', $product], ['orders.status', 3]])
                ->count();
            $total_pend_delivery_order = DB::table('order_products')
                ->join('orders', 'order_products.order_id', 'orders.id')
                ->where([['order_products.product_id', $product], ['orders.status', 5]])
                ->count();
            $total_on_delivery_order = DB::table('order_products')
                ->join('orders', 'order_products.order_id', 'orders.id')
                ->where([['order_products.product_id', $product], ['orders.status', 6]])
                ->count();
            $total_courier_hold_order = DB::table('order_products')
                ->join('orders', 'order_products.order_id', 'orders.id')
                ->where([['order_products.product_id', $product], ['orders.status', 8]])
                ->count();
            $total_return_order = DB::table('order_products')
                ->join('orders', 'order_products.order_id', 'orders.id')
                ->where([['order_products.product_id', $product], ['orders.status', 7]])
                ->count();
            $total_deliver_order = DB::table('order_products')
                ->join('orders', 'order_products.order_id', 'orders.id')
                ->where([['order_products.product_id', $product], ['orders.status', 1]])
                ->count();
            $total_order = DB::table('order_products')
                ->join('orders', 'order_products.order_id', 'orders.id')
                ->where('order_products.product_id', $product)
                ->count();
            $total_active = ($total_pend_delivery_order + $total_pend_pay_order + $total_process_order);
            $data[] = [
                $prod_name,
                [
                    'total_order' => $total_order,
                    'total_active' => $total_active,
                    'total_process_order' => $total_process_order,
                    'total_nr_1_order' => $total_nr_1_order,
                    'total_nr_2_order' => $total_nr_2_order,
                    'total_hold_order' => $total_hold_order,
                    'total_cancel_order' => $total_cancel_order,
                    'total_pend_pay_order' => $total_pend_pay_order,
                    'total_pend_delivery_order' => $total_pend_delivery_order,
                    'total_on_delivery_order' => $total_on_delivery_order,
                    'total_courier_hold_order' => $total_courier_hold_order,
                    'total_return_order' => $total_return_order,
                    'total_deliver_order' => $total_deliver_order,
                ],
            ];
        }

        return response()->json($data);
    }

    public function ordersProduct(Request $request)
    {
        $access_token = DB::table('web_settings')->select('api_access_token')->where('id', 1)->first()->api_access_token;
        abort_if($access_token != $request->bearerToken(), 403, 'Unauthorized Access');

        if ($request->input('paginate') != null) {
            $paginate = $request->input('paginate');
        } else {
            $paginate = 10;
        }

        $pr = DB::table('order_products')
            ->select('order_products.product_id')
            ->groupBy('order_products.product_id')
            ->pluck('product_id')->toArray();

        $products = [];
        foreach ($pr as $it) {
            $products[$it] = DB::table('products')
                ->where('id', $it)
                ->first()->name;
        }
        // dd($products);
        // dd($request->input('status'));
        $custom_range = $request->input('custom_range');
        $prod_id = $request->input('prod_id');
        $status = $request->input('status');
        $data['products'] = $products;
        if ($request->input('prod_id')) {
            $data['total_order'] = OrderProduct::query();
            $data['total_hold_order'] = OrderProduct::query();
            $data['total_deliver_order'] = OrderProduct::query();
            $data['total_process_order'] = OrderProduct::query();
            $data['total_pend_pay_order'] = OrderProduct::query();
            $data['total_cancel_order'] = OrderProduct::query();
            $data['total_pending_delivery_order'] = OrderProduct::query();
            $data['total_on_delivery_order'] = OrderProduct::query();
            $data['total_return_order'] = OrderProduct::query();
            $data['total_courier_hold_order'] = OrderProduct::query();
            $data['total_nr_1_order'] = OrderProduct::query();
            $data['total_nr_2_order'] = OrderProduct::query();
            $data['orders'] = Order::query();

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

                $data['orders']->whereDate('created_at', $custom_range);
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

                $data['orders']->whereDate('created_at', $custom_range);
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

                $data['orders']->where('created_at', '>=', $custom_range);
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

                $data['orders']->whereMonth('created_at', $custom_range);
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

                $data['orders']->whereBetween('created_at', [$sd, $ed]);
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

                $data['orders']->whereBetween('created_at', [$sd, $ed]);
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

                $data['orders']->whereBetween('created_at', [$sd, $ed]);
            }

            $data['total_order'] = $data['total_order']->where('product_id', $request->input('prod_id'));
            $data['total_hold_order'] = $data['total_hold_order']->where([['product_id', $request->input('prod_id')], ['status', 0]]);
            $data['total_deliver_order'] = $data['total_deliver_order']->where([['product_id', $request->input('prod_id')], ['status', 1]]);
            $data['total_process_order'] = $data['total_process_order']->where([['product_id', $request->input('prod_id')], ['status', 2]]);
            $data['total_pend_pay_order'] = $data['total_pend_pay_order']->where([['product_id', $request->input('prod_id')], ['status', 3]]);
            $data['total_cancel_order'] = $data['total_cancel_order']->where([['product_id', $request->input('prod_id')], ['status', 4]]);
            $data['total_pending_delivery_order'] = $data['total_pending_delivery_order']->where([['product_id', $request->input('prod_id')], ['status', 5]]);
            $data['total_on_delivery_order'] = $data['total_on_delivery_order']->where([['product_id', $request->input('prod_id')], ['status', 6]]);
            $data['total_return_order'] = $data['total_return_order']->where([['product_id', $request->input('prod_id')], ['status', 7]]);
            $data['total_courier_hold_order'] = $data['total_courier_hold_order']->where([['product_id', $request->input('prod_id')], ['status', 7]]);
            $data['total_nr_1_order'] = $data['total_nr_1_order']->where([['product_id', $request->input('prod_id')], ['status', 7]]);
            $data['total_nr_2_order'] = $data['total_nr_2_order']->where([['product_id', $request->input('prod_id')], ['status', 7]]);

            $data['orders'] = $data['orders']->with('get_products', 'get_courier', 'get_assigned')->whereHas('get_products', function ($qry) use ($prod_id): void {
                $qry->join('products', 'products.id', 'order_products.product_id')->where('products.id', $prod_id);
            });

            if ($request->input('status') != null) {
                $data['orders'] = $data['orders']->where('status', $request->input('status'));
            }

            $data['total_order'] = $data['total_order']->leftJoin('orders', 'orders.id', 'order_products.order_id')->count();
            $data['total_hold_order'] = $data['total_hold_order']->leftJoin('orders', 'orders.id', 'order_products.order_id')->count();
            $data['total_deliver_order'] = $data['total_deliver_order']->leftJoin('orders', 'orders.id', 'order_products.order_id')->count();
            $data['total_process_order'] = $data['total_process_order']->leftJoin('orders', 'orders.id', 'order_products.order_id')->count();
            $data['total_pend_pay_order'] = $data['total_pend_pay_order']->leftJoin('orders', 'orders.id', 'order_products.order_id')->count();
            $data['total_cancel_order'] = $data['total_cancel_order']->leftJoin('orders', 'orders.id', 'order_products.order_id')->count();
            $data['total_pending_delivery_order'] = $data['total_pending_delivery_order']->leftJoin('orders', 'orders.id', 'order_products.order_id')->count();
            $data['total_on_delivery_order'] = $data['total_on_delivery_order']->leftJoin('orders', 'orders.id', 'order_products.order_id')->count();
            $data['total_return_order'] = $data['total_return_order']->leftJoin('orders', 'orders.id', 'order_products.order_id')->count();
            $data['total_courier_hold_order'] = $data['total_courier_hold_order']->leftJoin('orders', 'orders.id', 'order_products.order_id')->count();
            $data['total_nr_1_order'] = $data['total_nr_1_order']->leftJoin('orders', 'orders.id', 'order_products.order_id')->count();
            $data['total_nr_2_order'] = $data['total_nr_2_order']->leftJoin('orders', 'orders.id', 'order_products.order_id')->count();
            // dd($data['total_order']);

            $data['count'] = $data['orders']->count();
            $data['orders'] = $data['orders']->with('get_products.get_product', 'get_courier', 'get_assigned.get_employee')
                ->select('invoice_id', 'customer_name', 'customer_phone', 'customer_address', 'total', 'order_date', 'created_at', 'status', 'staff_note', 'courier_note', 'courier_status', 'id', 'ip_address', 'courier_id', 'paid', 'due', 'pathao_consignment_id', 'redx_tracking_id', 'payment_status')
                ->orderBy('id', 'desc')->paginate($paginate);
            foreach ($data['orders'] as $key => $order) {
                $duplicate_orders = DB::table('orders')->where('customer_phone', $order->customer_phone)->count();
                $data['orders'][$key]->duplicate_orders = $duplicate_orders;
            }
            $data['orders']->appends(['paginate' => $paginate, 'prod_id' => $prod_id]);
            // dd($data['orders']);
        } else {
            $data['total_order'] = 0;
            $data['total_hold_order'] = 0;
            $data['total_deliver_order'] = 0;
            $data['total_process_order'] = 0;
            $data['total_pend_pay_order'] = 0;
            $data['total_cancel_order'] = 0;
            $data['total_pending_delivery_order'] = 0;
            $data['total_on_delivery_order'] = 0;
            $data['total_return_order'] = 0;
            $data['total_courier_hold_order'] = 0;
            $data['total_nr_1_order'] = 0;
            $data['total_nr_2_order'] = 0;
            $data['orders'] = [];
        }

        // dd($data['orders']);
        return response()->json($data);
    }
}
