<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Services\BanglaToEnglishConverter;
use App\Models\Employee;
use App\Models\Manager;
use App\Models\Order;
use App\Models\WebSettings;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {

        $top_cities = Order::whereNotNull('courier_city_id')->select('courier_city_id', DB::raw('count(*) as total'))
            ->groupBy('courier_city_id')
            ->orderBy('total', 'desc')
            ->get();
        // dd($top_cities);

        // top sell item from order product table with quantity
        $top_sell = DB::table('order_products')
            ->select('product_id', DB::raw('sum(qty) as total'))
            ->groupBy('product_id')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        // dd($top_sell);
        if (Auth::guard('admin')->check() || Auth::guard('manager')->check()) {
            $data['total_revenue'] = DB::table('orders')->where('status', 1)->sum('total');
            $data['total_customer'] = DB::table('users')->count();
            $data['total_product'] = DB::table('products')->count();
            $data['employees'] = DB::table('employees')->select('id', 'name', 'status', 'last_seen', 'last_login_ip')->whereNotNull('last_seen')->where('status', 1)->get();
            $data['admins'] = DB::table('admins')->select('id', 'name', 'status', 'last_seen', 'last_login_ip')->whereNotNull('last_seen')->where([['status', 1], ['id', '!=', 1]])->get();
            $data['managers'] = DB::table('managers')->select('id', 'name', 'status', 'last_seen', 'last_login_ip')->whereNotNull('last_seen')->where('status', 1)->get();

            if (Auth::guard('admin')->check()) {
                $data['total_staff'] = (DB::table('admins')->count() + DB::table('employees')->count() + DB::table('managers')->count() - 1);
            } elseif (Auth::guard('manager')->check()) {
                $data['total_staff'] = (DB::table('employees')->count() + DB::table('managers')->count() - 2);
            }

            $data['recent_orders'] = DB::table('orders')->select('id', 'order_date', 'customer_name', 'customer_phone', 'total', 'status')
                ->orderBy('id', 'desc')
                ->limit(10)
                ->get();
            $data['total_order'] = DB::table('orders')->where('deleted_at', null)->count();
            $data['total_hold_order'] = DB::table('orders')->where('deleted_at', null)->where('status', 0)->count();
            $data['total_deliver_order'] = DB::table('orders')->where('deleted_at', null)->where('status', 1)->count();
            $data['total_process_order'] = DB::table('orders')->where('deleted_at', null)->where('status', 2)->count();
            $data['total_pend_pay_order'] = DB::table('orders')->where('deleted_at', null)->where('status', 3)->count();
            $data['total_cancel_order'] = DB::table('orders')->where('deleted_at', null)->where('status', 4)->count();
            $data['total_pending_invoice_order'] = DB::table('orders')->where('deleted_at', null)->where('status', 5)->count();
            $data['total_on_delivery_order'] = DB::table('orders')->where('deleted_at', null)->where('status', 6)->count();
            $data['total_pending_return_order'] = DB::table('orders')->where('deleted_at', null)->where('status', 7)->count();
            $data['total_courier_hold_order'] = DB::table('orders')->where('deleted_at', null)->where('status', 8)->count();
            $data['total_nr_1_order'] = DB::table('orders')->where('deleted_at', null)->where('status', 9)->count();
            $data['total_invoiced_order'] = DB::table('orders')->where('deleted_at', null)->where('status', 10)->count();
            $data['total_return_order'] = DB::table('orders')->where('deleted_at', null)->where('status', 11)->count();
            $data['total_incomplete_order'] = DB::table('orders')->where('deleted_at', null)->where('status', 12)->count();
            $data['total_confirmed_order'] = DB::table('orders')->where('deleted_at', null)->where('status', 13)->count();
            $data['total_stock_out_order'] = DB::table('orders')->where('deleted_at', null)->where('status', 14)->count();
            $data['total_partial_delivery_order'] = DB::table('orders')->where('deleted_at', null)->where('status', 15)->count();
            $data['total_lost_order'] = DB::table('orders')->where('deleted_at', null)->where('status', 16)->count();

            $data['today_all_orders'] = DB::table('orders')->whereDate('order_date', \Illuminate\Support\Facades\Date::today())
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
            $data['today_pending_invoice_orders'] = DB::table('orders')->where('status', 5)
                ->whereDate('order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_on_delivery_orders'] = DB::table('orders')->where('status', 6)
                ->whereDate('order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_pending_return_orders'] = DB::table('orders')->where('status', 7)
                ->whereDate('order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_courier_hold_orders'] = DB::table('orders')->where('status', 8)
                ->whereDate('order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_nr_1_orders'] = DB::table('orders')->where('status', 9)
                ->whereDate('order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_invoiced_orders'] = DB::table('orders')->where('status', 10)
                ->whereDate('order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_return_orders'] = DB::table('orders')->where('status', 11)
                ->whereDate('order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_incomplete_orders'] = DB::table('orders')->where('deleted_at', null)->where('status', 12)
                ->whereDate('order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_confirmed_orders'] = DB::table('orders')->where('deleted_at', null)->where('status', 13)
                ->whereDate('order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_stock_out_orders'] = DB::table('orders')->where('status', 14)
                ->whereDate('order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_partial_delivery_orders'] = DB::table('orders')->where('status', 15)
                ->whereDate('order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_lost_orders'] = DB::table('orders')->where('status', 16)
                ->whereDate('order_date', \Illuminate\Support\Facades\Date::today())
                ->count();

        } elseif (Auth::guard('employee')->check()) {
            // $data['recent_orders'] = OrderAssign::with('get_order')->where('employee_id', Auth::guard('employee')->id())->orderBy('id', 'desc')->limit(10)->get();
            /*$data['recent_orders'] = Order::whereHas('get_assigned',function ($q){
            $q->where('employee_id',Auth::guard('employee')->id());
            })->orderBy('id', 'desc')->limit(10)->get();*/

            $data['recent_orders'] = DB::table('orders')
                ->leftJoin('order_assigns', 'order_assigns.order_id', 'orders.id')
                ->select('orders.id', 'orders.order_date', 'orders.customer_name', 'orders.customer_phone', 'orders.total', 'orders.status')
                ->where('order_assigns.employee_id', Auth::guard('employee')->id())
                ->orderBy('orders.id', 'desc')->limit(10)->get();

            $data['total_order'] = DB::table('order_assigns')
                ->where('employee_id', Auth::guard('employee')->id())
                ->count();
            $data['total_hold_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 0]])
                ->count();
            $data['total_deliver_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 1]])
                ->count();
            $data['total_process_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 2]])
                ->count();
            $data['total_pend_pay_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 3]])
                ->count();
            $data['total_cancel_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 4]])
                ->count();
            $data['total_pending_invoice_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 5]])
                ->count();
            $data['total_on_delivery_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 6]])
                ->count();
            $data['total_pending_return_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 7]])
                ->count();
            $data['total_courier_hold_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 8]])
                ->count();
            $data['total_nr_1_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 9]])
                ->count();
            $data['total_invoiced_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 10]])
                ->count();

            $data['total_return_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 11]])
                ->count();
            $data['total_incomplete_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 12]])
                ->count();
            $data['total_confirmed_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 13]])
                ->count();
            $data['total_stock_out_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 14]])
                ->count();
            $data['total_partial_delivery_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 15]])
                ->count();
            $data['total_lost_order'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 16]])
                ->count();

            $data['today_all_orders'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where('order_assigns.employee_id', Auth::guard('employee')->id())
                ->whereDate('orders.order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_hold_orders'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 0]])
                ->whereDate('orders.order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_deliver_orders'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 1]])
                ->whereDate('orders.order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_process_orders'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 2]])
                ->whereDate('orders.order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_pend_pay_orders'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 3]])
                ->whereDate('orders.order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_cancel_orders'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 4]])
                ->whereDate('orders.order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_pending_invoice_orders'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 5]])
                ->whereDate('orders.order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_on_delivery_orders'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 6]])
                ->whereDate('orders.order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_pending_return_orders'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 7]])
                ->whereDate('orders.order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_courier_hold_orders'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 8]])
                ->whereDate('orders.order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_nr_1_orders'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 9]])
                ->whereDate('orders.order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_invoiced_orders'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 10]])
                ->whereDate('orders.order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_return_orders'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 11]])
                ->whereDate('orders.order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            // $data['today_incomplete_orders'] = DB::table('order_assigns')
            //     ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
            //     ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 12]])
            //     ->whereDate('orders.order_date', Carbon::today())
            //     ->count();
            $data['today_confirmed_orders'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 13]])
                ->whereDate('orders.order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_stock_out_orders'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 14]])
                ->whereDate('orders.order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_partial_delivery_orders'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 15]])
                ->whereDate('orders.order_date', \Illuminate\Support\Facades\Date::today())
                ->count();
            $data['today_lost_orders'] = DB::table('order_assigns')
                ->leftJoin('orders', 'orders.id', 'order_assigns.order_id')
                ->where([['order_assigns.employee_id', Auth::guard('employee')->id()], ['status', 16]])
                ->whereDate('orders.order_date', \Illuminate\Support\Facades\Date::today())
                ->count();

            // dd($data['today_hold_orders']);
        } else {
            $data = [];
        }

        // dd($data['recent_orders']);
        return view('backEnd.admin.dashboard', compact('data', 'top_cities', 'top_sell'));
    }

    // change password
    public function change_pass()
    {
        return view('backEnd.admin.change_pass');
    }

    public function update_pass(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $user_id = Auth::guard('admin')->id();
            if (Hash::check($request->old_pass, Admin::find($user_id)->password)) {
                Admin::find($user_id)->update([
                    'password' => Hash::make($request->password),
                ]);

                $this->guard_admin()->logout();

                $request->session()->invalidate();

                $request->session()->regenerateToken();

                return $this->loggedOut($request) ?: to_route('admin.home')->with('success', 'Password Changed Successfully');
            } else {
                return back()->with('error', 'Incorrect Old Password');
            }
        } elseif (Auth::guard('manager')->check()) {
            $user_id = Auth::guard('manager')->id();
            if (Hash::check($request->old_pass, Manager::find($user_id)->password)) {
                Manager::find($user_id)->update([
                    'password' => Hash::make($request->password),
                ]);

                $this->guard_manager()->logout();

                $request->session()->invalidate();

                $request->session()->regenerateToken();

                return $this->loggedOut($request) ?: to_route('manager.home')->with('success', 'Password Changed Successfully');
            } else {
                return back()->with('error', 'Incorrect Old Password');
            }
        } elseif (Auth::guard('employee')->check()) {
            $user_id = Auth::guard('employee')->id();
            if (Hash::check($request->old_pass, Employee::find($user_id)->password)) {
                Employee::find($user_id)->update([
                    'password' => Hash::make($request->password),
                ]);

                $this->guard_employee()->logout();

                $request->session()->invalidate();

                $request->session()->regenerateToken();

                return $this->loggedOut($request) ?: to_route('employee.home')->with('success', 'Password Changed Successfully');
            } else {
                return back()->with('error', 'Incorrect Old Password');
            }
        } else {
            return back()->with('warning', 'Something Went Wrong!');
        }
    }

    protected function loggedOut(Request $request) {}

    protected function guard_admin()
    {
        return Auth::guard('admin');
    }

    protected function guard_manager()
    {
        return Auth::guard('manager');
    }

    protected function guard_employee()
    {
        return Auth::guard('employee');
    }

    public function stock()
    {
        return view('backEnd.admin.stock');
    }

    public function sendSms(Request $request)
    {
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
        // return back()->with('success','SMS Sent Successfully');
    }

    public function generateAPIToken()
    {
        WebSettings::find(1)->update([
            'api_access_token' => Str::random(150),
        ]);

        return back()->with('success', 'API Token Generated Successfully');
    }

    public function fakeRemove($id)
    {
        DB::table('orders')->where('id', $id)->update([
            'is_fake' => 0,
        ]);

        return back()->with('success', 'Removed Successfully');
    }

    public function fraudCheck(Request $request, $id)
    {
        $order = Order::select('id', 'status', 'customer_phone', 'customer_activity')->find($id);
        // dd($customer_number);
        if (strlen((string) $order->customer_phone) == 11) {
            /*$curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://courierrank.com/api/get-customer-details/' . $order->customer_phone,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Bearer ' . env('TJ_FC_API'),
                ),
            ));
            $response = curl_exec($curl);*/

            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://bdcourier.com/api/courier-check?phone='.$order->customer_phone,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer '.env('TJ_FC_API'),
                ],
            ]);

            $response = curl_exec($curl);

            curl_close($curl);

            // dd(json_decode($response));
            /*if (json_decode($response) && json_decode($response)->phone) {
                $data = [
                    'total' => json_decode($response)->pathao_delivered + json_decode($response)->pathao_returned + json_decode($response)->steadfast_delivered + json_decode($response)->steadfast_returned + json_decode($response)->redx_delivered + json_decode($response)->redx_returned,
                    'total_delivered' => json_decode($response)->pathao_delivered + json_decode($response)->steadfast_delivered + json_decode($response)->redx_delivered,
                    'total_returned' => json_decode($response)->pathao_returned + json_decode($response)->steadfast_returned + json_decode($response)->redx_returned,
                    'pathao_delivered' => json_decode($response)->pathao_delivered,
                    'pathao_returned' => json_decode($response)->pathao_returned,
                    'steadfast_delivered' => json_decode($response)->steadfast_delivered,
                    'steadfast_returned' => json_decode($response)->steadfast_returned,
                    'redx_delivered' => json_decode($response)->redx_delivered,
                    'redx_returned' => json_decode($response)->redx_returned,
                ];
            } else {
                return back()->with('error', 'Something went wrong');
            }*/
            if (json_decode($response) && json_decode($response)->status == 'success') {
                $data = [
                    'total' => json_decode($response)->courierData->summary->total_parcel,
                    'total_delivered' => json_decode($response)->courierData->summary->success_parcel,
                    'total_returned' => json_decode($response)->courierData->summary->cancelled_parcel,
                    'pathao_delivered' => json_decode($response)->courierData->pathao->success_parcel,
                    'pathao_returned' => json_decode($response)->courierData->pathao->cancelled_parcel,
                    'steadfast_delivered' => json_decode($response)->courierData->steadfast->success_parcel,
                    'steadfast_returned' => json_decode($response)->courierData->steadfast->cancelled_parcel,
                    'redx_delivered' => json_decode($response)->courierData->redx->success_parcel,
                    'redx_returned' => json_decode($response)->courierData->redx->cancelled_parcel,
                ];

                // dd($data);
                $order->update([
                    'customer_activity' => json_encode($data),
                ]);

                return back()->with('success', 'Activity Updated Successfully');
            } else {
                return back()->with('error', 'Something went wrong');
            }
        } else {
            return back()->with('warning', 'Phone number is not 11 digit');
        }
    }
}
