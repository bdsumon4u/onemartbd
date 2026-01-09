<?php

namespace App\Http\Controllers;

use App\Order;
use App\OrderAssign;
use App\OrderProduct;
use App\Product;
use App\WebSettings;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function employeeOrders(Request $request)
    {
        if ($request->input('paginate') != null) {
            $paginate = $request->input('paginate');
        } else {
            $paginate = 10;
        }
        $query = $request->input('query') ?? null;
        $custom_range = $request->input('custom_range');
        //dd($request->input('status'));
        $emp_id = $request->input('emp_id');
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
                $custom_range = Carbon::today()->toDateTimeString();

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
                $custom_range = Carbon::yesterday()->toDateTimeString();

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
                $custom_range = Carbon::now()->subDays(7)->toDateTimeString();

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
                $custom_range = Carbon::now()->month;

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
                $sd = Carbon::now()->subMonth()->startOfMonth()->toDateTimeString();
                $ed = Carbon::now()->subMonth()->endOfMonth()->toDateTimeString();

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
                $sd = Carbon::now()->subMonths(6)->startOfMonth()->toDateTimeString();
                $ed = Carbon::now()->toDateTimeString();

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
                $sd = Carbon::parse($request->input('start_date'))->startOfDay()->toDateTimeString();
                $ed = Carbon::parse($request->input('end_date'))->endOfDay()->toDateTimeString();

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

            $data['total_order'] = $data['total_order']->where('order_assigns.employee_id', $request->input('emp_id'));
            $data['total_hold_order'] = $data['total_hold_order']->where([['order_assigns.employee_id', $request->input('emp_id')], ['status', 0]]);
            $data['total_deliver_order'] = $data['total_deliver_order']->where([['order_assigns.employee_id', $request->input('emp_id')], ['status', 1]]);
            $data['total_process_order'] = $data['total_process_order']->where([['order_assigns.employee_id', $request->input('emp_id')], ['status', 2]]);
            $data['total_pend_pay_order'] = $data['total_pend_pay_order']->where([['order_assigns.employee_id', $request->input('emp_id')], ['status', 3]]);
            $data['total_cancel_order'] = $data['total_cancel_order']->where([['order_assigns.employee_id', $request->input('emp_id')], ['status', 4]]);
            $data['total_pending_delivery_order'] = $data['total_pending_delivery_order']->where([['order_assigns.employee_id', $request->input('emp_id')], ['status', 5]]);
            $data['total_on_delivery_order'] = $data['total_on_delivery_order']->where([['order_assigns.employee_id', $request->input('emp_id')], ['status', 6]]);
            $data['total_return_order'] = $data['total_return_order']->where([['order_assigns.employee_id', $request->input('emp_id')], ['status', 7]]);
            $data['total_courier_hold_order'] = $data['total_courier_hold_order']->where([['order_assigns.employee_id', $request->input('emp_id')], ['status', 8]]);
            $data['total_nr_1_order'] = $data['total_nr_1_order']->where([['order_assigns.employee_id', $request->input('emp_id')], ['status', 9]]);
            $data['total_nr_2_order'] = $data['total_nr_2_order']->where([['order_assigns.employee_id', $request->input('emp_id')], ['status', 10]]);

            $data['orders'] = $data['orders']->with('get_products', 'get_courier', 'get_assigned')->whereHas('get_assigned', function ($qry) use ($emp_id) {
                $qry->where('employee_id', $emp_id);
            });

            if ($request->input('status') != null) {
                $data['orders'] = $data['orders']->where('status', $request->input('status'));
            }

            if ($request->input('query')) {
                $data['orders']->where('customer_phone', 'LIKE', "%{$request->input('query')}%");
                $data['orders']->orWhere('customer_name', 'LIKE', "%{$request->input('query')}%");
                $data['orders']->orWhere('invoice_id', 'LIKE', "%{$request->input('query')}%");

                $data['orders']->orWhereHas('get_products', function ($p) use ($query) {
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
            //dd($data['total_order']);

            $data['count'] = $data['orders']->get()->count();
            $data['orders'] = $data['orders']->orderBy('id', 'desc')->paginate($paginate);
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
        //dd($data['orders']);
        return view('backEnd.admin.reports.employee_orders.index', compact('data', 'emp_id', 'status'));
    }

    public function orderStatusP()
    {
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
        foreach ($total_order as $key => $product) {
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
                $key => $prod_name,
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
        return view('backEnd.admin.reports.order_status_p.index', compact('data'));
    }

    public function ordersProduct(Request $request)
    {
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
        //dd($products);
        //dd($request->input('status'));
        $custom_range = $request->input('custom_range');
        $prod_id = $request->input('prod_id');
        $status = $request->input('status');
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
                $custom_range = Carbon::today()->toDateTimeString();

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
                $custom_range = Carbon::yesterday()->toDateTimeString();

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
                $custom_range = Carbon::now()->subDays(7)->toDateTimeString();

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
                $custom_range = Carbon::now()->month;

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
                $sd = Carbon::now()->subMonth()->startOfMonth()->toDateTimeString();
                $ed = Carbon::now()->subMonth()->endOfMonth()->toDateTimeString();

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
                $sd = Carbon::now()->subMonths(6)->startOfMonth()->toDateTimeString();
                $ed = Carbon::now()->toDateTimeString();

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
                $sd = Carbon::parse($request->input('start_date'))->startOfDay()->toDateTimeString();
                $ed = Carbon::parse($request->input('end_date'))->endOfDay()->toDateTimeString();

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
            $data['total_courier_hold_order'] = $data['total_courier_hold_order']->where([['product_id', $request->input('prod_id')], ['status', 8]]);
            $data['total_nr_1_order'] = $data['total_nr_1_order']->where([['product_id', $request->input('prod_id')], ['status', 9]]);
            $data['total_nr_2_order'] = $data['total_nr_2_order']->where([['product_id', $request->input('prod_id')], ['status', 10]]);

            $data['orders'] = $data['orders']->with('get_products', 'get_courier', 'get_assigned')->whereHas('get_products', function ($qry) use ($prod_id) {
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
            //dd($data['total_order']);

            $data['count'] = $data['orders']->count();
            $data['orders'] = $data['orders']->orderBy('id', 'desc')->paginate($paginate);
            $data['orders']->appends(['paginate' => $paginate, 'prod_id' => $prod_id]);
            //dd($data['orders']);
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
        //dd($data['orders']);
        return view('backEnd.admin.reports.orders_product.index', compact('data', 'products', 'status', 'prod_id'));
    }

    public function salesReport(Request $request)
    {
        $custom_range = $request->input('custom_range');
        $data = OrderProduct::query()->with('get_product')
            ->join('orders', 'orders.id', 'order_products.order_id')
            ->select('order_products.*', 'orders.created_at', 'orders.discount');
        $total_sales = 0;
        $total_discounts = 0;

        // dd($request->all());
        if ($custom_range == 'today') {
            $custom_range = Carbon::today()->toDateTimeString();
            $data->whereDate('orders.created_at', $custom_range);
            // dd($data->get());
        } elseif ($custom_range == 'yesterday') {
            $custom_range = Carbon::yesterday()->toDateTimeString();

            $data->whereDate('orders.created_at', $custom_range);
        } elseif ($custom_range == 'last_7_days') {
            $sd = Carbon::now()->subDays(7)->startOfDay()->toDateTimeString();
            $ed = Carbon::now()->toDateTimeString();
            $data->whereBetween('orders.created_at', [$sd, $ed]);
        } elseif ($custom_range == 'this_month') {
            $sd = Carbon::now()->startOfMonth()->toDateTimeString();
            $ed = Carbon::now()->endOfMonth()->toDateTimeString();

            $data->whereBetween('orders.created_at', [$sd, $ed]);
        } elseif ($custom_range == 'last_month') {
            $sd = Carbon::now()->subMonth()->startOfMonth()->toDateTimeString();
            $ed = Carbon::now()->subMonth()->endOfMonth()->toDateTimeString();

            $data->whereBetween('orders.created_at', [$sd, $ed]);
        } elseif ($custom_range == 'last_6_months') {
            $sd = Carbon::now()->subMonths(6)->startOfMonth()->toDateTimeString();
            $ed = Carbon::now()->toDateTimeString();

            $data->whereBetween('orders.created_at', [$sd, $ed]);
        } elseif ($request->input('start_date') && $request->input('end_date')) {
            $sd = Carbon::parse($request->input('start_date'))->startOfDay()->toDateTimeString();
            $ed = Carbon::parse($request->input('end_date'))->endOfDay()->toDateTimeString();

            $data->whereBetween('orders.created_at', [$sd, $ed]);
        }

        $orders = $data->where('status', 1)->get()->groupBy('product_id');
        foreach ($orders as $order) {
            $d[] = [
                'product_name' => $order->first()->get_product->name,
                'sales' => $order->sum('price'),
                'discount' => $order->sum('discount'),
            ];
        }
        //dd($d);
        foreach ($d as $dd) {
            $total_sales += $dd['sales'];
            $total_discounts += $dd['discount'];
        }

        return view('backEnd.admin.reports.sales.sales_report', compact('total_sales', 'total_discounts'));

    }

    public function profitLoss(Request $request)
    {
        // dd($request->all());
        $custom_range = $request->input('custom_range');
        $total_dollar = $request->input('total_dollar') ?? 0;
        $dollar_rate = $request->input('dollar_rate') ?? 0;
        $return_percentage = $request->input('return_percentage') ?? 0;
        $product_id = $request->input('product_id');
        $status = $request->input('status');
        $dollar_cost = $total_dollar * $dollar_rate;
        // dd($product_id);

        $data = Order::query()->with('get_products.get_product');
        $products = Product::latest()->get();
        // dd ($data);
        $total_sales = 0;
        $total_discounts = 0;

        // dd($total_dollar,$dollar_rate,$packaging_cost,$return_percentage);
        if ($custom_range == 'today') {
            $custom_range = Carbon::today()->toDateTimeString();
            $data->whereDate('orders.created_at', $custom_range);
            // $count = $data->where('status', 1)->count();
            // $orders = $data->where('status', 1)->get();
        } elseif ($custom_range == 'yesterday') {
            $custom_range = Carbon::yesterday()->toDateTimeString();
            $data->whereDate('orders.created_at', $custom_range);
        } elseif ($custom_range == 'last_7_days') {
            $sd = Carbon::now()->subDays(7)->startOfDay()->toDateTimeString();
            $ed = Carbon::now()->toDateTimeString();
            $data->whereBetween('orders.created_at', [$sd, $ed]);
        } elseif ($custom_range == 'this_month') {
            $sd = Carbon::now()->startOfMonth()->toDateTimeString();
            $ed = Carbon::now()->endOfMonth()->toDateTimeString();

            $data->whereBetween('orders.created_at', [$sd, $ed]);
        } elseif ($custom_range == 'last_month') {
            $sd = Carbon::now()->subMonth()->startOfMonth()->toDateTimeString();
            $ed = Carbon::now()->subMonth()->endOfMonth()->toDateTimeString();

            $data->whereBetween('orders.created_at', [$sd, $ed]);
        } elseif ($custom_range == 'last_6_months') {
            $sd = Carbon::now()->subMonths(6)->startOfMonth()->toDateTimeString();
            $ed = Carbon::now()->toDateTimeString();
            $data->whereBetween('orders.created_at', [$sd, $ed]);
        } elseif ($request->input('start_date') && $request->input('end_date')) {
            $sd = Carbon::parse($request->input('start_date'))->startOfDay()->toDateTimeString();
            $ed = Carbon::parse($request->input('end_date'))->endOfDay()->toDateTimeString();
            $data->whereBetween('orders.created_at', [$sd, $ed]);
        }
        // dd($data->get());

        $orders = $data->when($product_id, function ($q) use ($product_id) {
            $q->whereHas('get_products', function ($q) use ($product_id) {
                $q->where('product_id', $product_id);
            });
        })->when($status, function ($q) use ($status) {
            $q->where('status', $status);
        })->get();
        // dd($orders);
        $count = $data->count();
        // dd($count);
        if ($count > 0) {
            $cost_per_order = $total_dollar / $count;
        } else {
            $cost_per_order = 0; // or handle the case where $count is zero appropriately
        }
        $product_purchase_cost = 0;
        $total_sales = 0;
        $product_packaging_cost = 0;
        $discount = 0;
        $shipping_cost = 0;
        $courier_charge_cost = 0;
        foreach ($orders as $order) {
            // dd($order->courier_charge_cost);
            if ($product_id) {
                if ($order->discount > 0) {
                    $discount += $order->get_products->where('product_id', $product_id)->first()->get_order->discount / count($order->get_products);
                } else {
                    $discount = 0;
                }
                // dd($discount);
                $shipping_cost += $order->get_products->where('product_id', $product_id)->first()->get_order->shipping_cost / count($order->get_products);
                $courier_charge_cost += $order->get_products->where('product_id', $product_id)->first()->get_order->courier_charge_cost / count($order->get_products);

                $price = $order->get_products->where('product_id', $product_id)->first()->price;
                $qty = $order->get_products->where('product_id', $product_id)->first()->qty;
                $total_sales += $price * $qty;
                $product_purchase_cost += $order->get_products->where('product_id', $product_id)->first()->get_product->purchase_cost * $qty;
                $product_packaging_cost += $order->get_products->where('product_id', $product_id)->first()->get_product->packaging_cost * $qty;

                // dd($total_sales);
            } else {
                if ($order->discount > 0) {
                    $discount += $order->discount;
                } else {
                    $discount = 0;
                }
                $shipping_cost += $order->shipping_cost;
                $courier_charge_cost += $order->courier_charge_cost;
                $total_sales += $order->select(DB::raw('SUM(sub_total) as total_sales'))->where('id', $order->id)->first()->total_sales;
                foreach ($order->get_products as $product) {
                    // dd($product->get_product);
                    $product_purchase_cost += $product->get_product->purchase_cost * $product->qty;
                    $product_packaging_cost += $product->get_product->packaging_cost * $product->qty;
                }
            }
        }

        $grand_total = ($total_sales + $shipping_cost) - $discount;
        $courier_charge_profit = $shipping_cost - $courier_charge_cost;
        // dd( $shipping_cost );
        $return_cost = ($grand_total * $return_percentage) / 100;
        // dd($return_cost);
        $net_sales = $grand_total - $return_cost;
        $profit_loss = $net_sales - ($dollar_cost + $product_purchase_cost + $product_packaging_cost + $courier_charge_cost);
        // dd($courier_charge_cost);
        return view('backEnd.admin.reports.profit_loss', compact('dollar_cost', 'cost_per_order', 'product_purchase_cost', 'net_sales', 'product_packaging_cost', 'products', 'courier_charge_profit', 'grand_total', 'profit_loss', 'courier_charge_cost', 'return_cost', 'return_percentage'));
    }


    public function salesReportPrint(Request $request)
    {
        //dd($request->all());
        $custom_range = $request->input('custom_range');
        $data = OrderProduct::query()->with('get_product')
            ->join('orders', 'orders.id', 'order_products.order_id')
            ->select('order_products.*', 'orders.created_at', 'orders.discount');
        $cr = '';
        $s_date = '';
        $e_date = '';
        //dd($request->all());
        if ($custom_range == 'today') {
            $custom_range = Carbon::today()->toDateTimeString();
            $data->whereDate('orders.created_at', $custom_range);

            //show data into report print
            $cr = date('d/m/Y', strtotime($custom_range));
        } elseif ($custom_range == 'yesterday') {
            $custom_range = Carbon::yesterday()->toDateTimeString();

            $data->whereDate('orders.created_at', $custom_range);

            //show data into report print
            $cr = date('d/m/Y', strtotime($custom_range));
        } elseif ($custom_range == 'last_7_days') {
            $sd = Carbon::now()->subDays(7)->startOfDay()->toDateTimeString();
            $ed = Carbon::now()->toDateTimeString();
            $data->whereBetween('orders.created_at', [$sd, $ed]);

            //show data into report print
            $s_date = date('d/m/Y', strtotime($sd));
            $e_date = date('d/m/Y', strtotime($ed));
        } elseif ($custom_range == 'this_month') {
            $sd = Carbon::now()->startOfMonth()->toDateTimeString();
            $ed = Carbon::now()->endOfMonth()->toDateTimeString();

            $data->whereBetween('orders.created_at', [$sd, $ed]);

            //show data into report print
            $s_date = date('d/m/Y', strtotime($sd));
            $e_date = date('d/m/Y', strtotime($ed));
        } elseif ($custom_range == 'last_month') {
            $sd = Carbon::now()->subMonth()->startOfMonth()->toDateTimeString();
            $ed = Carbon::now()->subMonth()->endOfMonth()->toDateTimeString();

            $data->whereBetween('orders.created_at', [$sd, $ed]);

            //show data into report print
            $s_date = date('d/m/Y', strtotime($sd));
            $e_date = date('d/m/Y', strtotime($ed));
        } elseif ($custom_range == 'last_6_months') {
            $sd = Carbon::now()->subMonths(6)->startOfMonth()->toDateTimeString();
            $ed = Carbon::now()->toDateTimeString();

            $data->whereBetween('orders.created_at', [$sd, $ed]);

            //show data into report print
            $s_date = date('d/m/Y', strtotime($sd));
            $e_date = date('d/m/Y', strtotime($ed));
        } elseif ($request->input('start_date') && $request->input('end_date')) {
            $sd = Carbon::parse($request->input('start_date'))->startOfDay()->toDateTimeString();
            $ed = Carbon::parse($request->input('end_date'))->endOfDay()->toDateTimeString();

            $data->whereBetween('orders.created_at', [$sd, $ed]);

            //show data into report print
            $s_date = date('d/m/Y', strtotime($sd));
            $e_date = date('d/m/Y', strtotime($ed));
        }

        $orders = $data->where('status', 1)->get()->groupBy('product_id');
        foreach ($orders as $order) {
            $result[$order->first()->get_product->name] = [
                'sales' => $order->sum('price'),
                'discount' => $order->sum('discount'),
            ];
        }
        //dd($result);

        $settings = WebSettings::with('get_logo')->where('id', 1)->first();

        return view('backEnd.admin.reports.sales.sales_export', compact('result', 'settings', 'cr', 's_date', 'e_date'))->render();
    }
}
