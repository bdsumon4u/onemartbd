<?php

namespace App\Http\Controllers\API;

use App\Employee;
use App\Http\Controllers\Controller;
use App\Order;
use App\OrderAssign;
use App\OrderProduct;
use App\UserProducts;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StaffController extends Controller
{
    public function staffSync(Request $request)
    {
        $access_token = DB::table('web_settings')->select('api_access_token')->where('id', 1)->first()->api_access_token;
        abort_if($access_token != $request->bearerToken(), 403, 'Unauthorized Access');

        $employees = Employee::pluck('p_id')->toArray();
        foreach ($request->all() as $key => $item) {
            $array[$key] =  $item['id'];
            if (!in_array("", $employees)) {
                //return 1;
                if (in_array($item['id'], $employees)) {
                    //return 2;
                    Employee::where('p_id', $item['id'])->update([
                        'p_id' => $item['id'],
                        'name' => $item['name'],
                        'email' => $item['email'],
                        'phone' => $item['phone'],
                        'password' => $item['password'],
                        'status' => $item['status'],
                        'start_time' => $item['start_time'],
                        'end_time' => $item['end_time'],
                    ]);
                }else{
                    Employee::create([
                        'p_id' => $item['id'],
                        'name' => $item['name'],
                        'email' => $item['email'],
                        'phone' => $item['phone'],
                        'password' => $item['password'],
                        'status' => $item['status'],
                        'start_time' => $item['start_time'],
                        'end_time' => $item['end_time'],
                    ]);
                }
            } else {
                //return 3;
                $check_duplicate = Employee::where('email', $item['email'])->first();
                if ($check_duplicate) {
                    $check_duplicate->update([
                        'p_id' => $item['id'],
                        'name' => $item['name'],
                        'email' => $item['email'],
                        'phone' => $item['phone'],
                        'password' => $item['password'],
                        'status' => $item['status'],
                        'start_time' => $item['start_time'],
                        'end_time' => $item['end_time'],
                    ]);
                } else {
                    //return 4;
                    Employee::create([
                        'p_id' => $item['id'],
                        'name' => $item['name'],
                        'email' => $item['email'],
                        'phone' => $item['phone'],
                        'password' => $item['password'],
                        'status' => $item['status'],
                        'start_time' => $item['start_time'],
                        'end_time' => $item['end_time'],
                    ]);
                }
            }
            //return $item->id;
        }

        //return $array;
        foreach ($employees as $employee) {
            if (!in_array($employee,$array)){
                Employee::where('p_id',$employee)->delete();
            }
        }

        UserProducts::query()->truncate();
        //return $request->all();
    }
}
