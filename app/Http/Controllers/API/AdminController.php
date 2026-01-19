<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $access_token = DB::table('web_settings')->select('api_access_token')->where('id', 1)->first()->api_access_token;
        abort_if($access_token != $request->bearerToken(), 403, 'Unauthorized Access');
        $emp_id = DB::table('employees')->where('p_id', $request->input('emp_id'))->select('id')->first();
        $emp_id = $emp_id ? $emp_id->id : '';

        if ($request->input('role_id') == 1 || $request->input('role_id') == 2 || $request->input('role_id') == 3) {
            $data['total_revenue'] = DB::table('orders')->whereBetween('created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])->where('status', 1)->sum('total');
            $data['total_customer'] = DB::table('users')->whereBetween('created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])->count();
            $data['total_product'] = DB::table('products')->count();

            /*$data['recent_orders'] = DB::table('orders')->select('id', 'order_date', 'customer_name', 'customer_phone', 'total', 'status')
                ->orderBy('id', 'desc')
                ->limit(10)
                ->get();*/
            $data['total_orders'] = DB::table('orders')->whereBetween('created_at', [\Illuminate\Support\Facades\Date::now()->subDays(3), \Illuminate\Support\Facades\Date::now()])->count();
            $data['total_hold_order'] = DB::table('orders')->whereBetween('created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])->where('status', 0)->count();
            $data['total_deliver_order'] = DB::table('orders')->whereBetween('created_at', [\Illuminate\Support\Facades\Date::now()->subDays(2), \Illuminate\Support\Facades\Date::now()])->where('status', 1)->count();
            $data['total_process_order'] = DB::table('orders')->whereBetween('created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])->where('status', 2)->count();
            $data['total_pend_pay_order'] = DB::table('orders')->whereBetween('created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])->where('status', 3)->count();
            $data['total_cancel_order'] = DB::table('orders')->whereBetween('created_at', [\Illuminate\Support\Facades\Date::now()->subDays(2), \Illuminate\Support\Facades\Date::now()])->where('status', 4)->count();
            $data['total_pending_delivery_order'] = DB::table('orders')->whereBetween('created_at', [\Illuminate\Support\Facades\Date::now()->subDays(3), \Illuminate\Support\Facades\Date::now()])->where('status', 5)->count();
            $data['total_on_delivery_order'] = DB::table('orders')->whereBetween('created_at', [\Illuminate\Support\Facades\Date::now()->subDays(3), \Illuminate\Support\Facades\Date::now()])->where('status', 6)->count();
            $data['total_return_order'] = DB::table('orders')->whereBetween('created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])->where('status', 7)->count();
            $data['total_courier_hold_order'] = DB::table('orders')->whereBetween('created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])->where('status', 8)->count();
            $data['total_nr_1_order'] = DB::table('orders')->whereBetween('created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])->where('status', 9)->count();
            $data['total_nr_2_order'] = DB::table('orders')->whereBetween('created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])->where('status', 10)->count();

            $data['today_orders'] = DB::table('orders')->whereDate('order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_hold_orders'] = DB::table('orders')->where('status', 0)
                ->whereDate('order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_deliver_orders'] = DB::table('orders')->where('status', 1)
                ->whereDate('order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_process_orders'] = DB::table('orders')->where('status', 2)
                ->whereDate('order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_pend_pay_orders'] = DB::table('orders')->where('status', 3)
                ->whereDate('order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_cancel_orders'] = DB::table('orders')->where('status', 4)
                ->whereDate('order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_pending_delivery_orders'] = DB::table('orders')->where('status', 5)
                ->whereDate('order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_on_delivery_orders'] = DB::table('orders')->where('status', 6)
                ->whereDate('order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_return_orders'] = DB::table('orders')->where('status', 7)
                ->whereDate('order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_courier_hold_orders'] = DB::table('orders')->where('status', 8)
                ->whereDate('order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_nr_1_orders'] = DB::table('orders')->where('status', 9)
                ->whereDate('order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_nr_2_orders'] = DB::table('orders')->where('status', 10)
                ->whereDate('order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
        } elseif ($request->input('role_id') == 4) {
            $data['total_revenue'] = DB::table('orders')->whereBetween('created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])->where('status', 1)->sum('total');
            $data['total_customer'] = DB::table('users')->whereBetween('created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])->count();
            $data['total_product'] = DB::table('products')->count();

            $data['total_orders'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(3), \Illuminate\Support\Facades\Date::now()])
                ->where('employee_id', $emp_id)
                ->count();
            $data['total_hold_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])
                ->where([['order_assigns.employee_id', $emp_id], ['status', 0]])
                ->count();
            $data['total_deliver_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(2), \Illuminate\Support\Facades\Date::now()])
                ->where([['order_assigns.employee_id', $emp_id], ['status', 1]])
                ->count();
            $data['total_process_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])
                ->where([['order_assigns.employee_id', $emp_id], ['status', 2]])
                ->count();
            $data['total_pend_pay_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])
                ->where([['order_assigns.employee_id', $emp_id], ['status', 3]])
                ->count();
            $data['total_cancel_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(2), \Illuminate\Support\Facades\Date::now()])
                ->where([['order_assigns.employee_id', $emp_id], ['status', 4]])
                ->count();
            $data['total_pending_delivery_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(3), \Illuminate\Support\Facades\Date::now()])
                ->where([['order_assigns.employee_id', $emp_id], ['status', 5]])
                ->count();
            $data['total_on_delivery_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(3), \Illuminate\Support\Facades\Date::now()])
                ->where([['order_assigns.employee_id', $emp_id], ['status', 6]])
                ->count();
            $data['total_return_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])
                ->where([['order_assigns.employee_id', $emp_id], ['status', 7]])
                ->count();
            $data['total_courier_hold_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])
                ->where([['order_assigns.employee_id', $emp_id], ['status', 8]])
                ->count();
            $data['total_nr_1_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])
                ->where([['order_assigns.employee_id', $emp_id], ['status', 9]])
                ->count();
            $data['total_nr_2_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')->whereBetween('orders.created_at', [\Illuminate\Support\Facades\Date::now()->subDays(7), \Illuminate\Support\Facades\Date::now()])
                ->where([['order_assigns.employee_id', $emp_id], ['status', 10]])
                ->count();

            $data['today_orders'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where('order_assigns.employee_id', $emp_id)
                ->whereDate('orders.order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_hold_orders'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', $emp_id], ['status', 0]])
                ->whereDate('orders.order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_deliver_orders'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', $emp_id], ['status', 1]])
                ->whereDate('orders.order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_process_orders'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', $emp_id], ['status', 2]])
                ->whereDate('orders.order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_pend_pay_orders'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', $emp_id], ['status', 3]])
                ->whereDate('orders.order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_cancel_orders'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', $emp_id], ['status', 4]])
                ->whereDate('orders.order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_pending_delivery_orders'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', $emp_id], ['status', 5]])
                ->whereDate('orders.order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_on_delivery_orders'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', $emp_id], ['status', 6]])
                ->whereDate('orders.order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_return_orders'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', $emp_id], ['status', 7]])
                ->whereDate('orders.order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_courier_hold_orders'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', $emp_id], ['status', 8]])
                ->whereDate('orders.order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_nr_1_orders'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', $emp_id], ['status', 9]])
                ->whereDate('orders.order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_nr_2_orders'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', $emp_id], ['status', 10]])
                ->whereDate('orders.order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            // dd($data['today_hold_orders']);
        } else {
            $data = [];
        }

        // dd($data['recent_orders']);
        return response()->json($data);
    }
}
