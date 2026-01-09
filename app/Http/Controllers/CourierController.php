<?php

namespace App\Http\Controllers;

use App\Courier;
use App\CourierCity;
use App\CourierZone;
use App\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CourierController extends Controller
{
    public function index()
    {
        $data = Courier::all();
        return view('backEnd.admin.couriers.index', compact('data'));
    }

    public function store(Request $request)
    {
        if ($request->is_city && $request->is_city == 'on') {
            $is_city = 1;
        } else {
            $is_city = 0;
        }

        if ($request->is_zone && $request->is_zone == 'on') {
            $is_zone = 1;
        } else {
            $is_zone = 0;
        }

        $input = array_merge($request->all(), [
            'is_city' => $is_city,
            'is_zone' => $is_zone,
        ]);
        Courier::create($input);
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.courier')->with('success', 'Courier Added Successfully');
        } elseif (Auth::guard('manager')->check()) {
            return redirect()->route('manager.courier')->with('success', 'Courier Added Successfully');
        } else {
            return back()->with('warning', 'Something Went Wrong');
        }
    }

    public function update(Request $request)
    {
        if ($request->is_city && $request->is_city == 'on') {
            $is_city = 1;
        } else {
            $is_city = 0;
        }

        if ($request->is_zone && $request->is_zone == 'on') {
            $is_zone = 1;
        } else {
            $is_zone = 0;
        }

        $input = array_merge($request->all(), [
            'is_city' => $is_city,
            'is_zone' => $is_zone,
        ]);
        Courier::find($request->id)->update($input);
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.courier')->with('success', 'Courier Updated Successfully');
        } elseif (Auth::guard('manager')->check()) {
            return redirect()->route('manager.courier')->with('success', 'Courier Updated Successfully');
        } else {
            return back()->with('warning', 'Something Went Wrong');
        }

    }

    public function delete($id)
    {
        $has_courier = Order::where('courier_id', $id)->first();
        if ($has_courier) {
            return back()->with('warning', 'This Product Already In Order');
        } else {
            CourierCity::where('courier_id', $id)->delete();
            CourierZone::where('courier_id', $id)->delete();
            Courier::find($id)->delete();
            return back()->with('success', 'Courier Deleted Successfully');
        }

    }

    ////for city
    public function cityIndex()
    {
        $data = CourierCity::all();
        $couriers = Courier::pluck('courier_name', 'id');
        return view('backEnd.admin.couriers.cities.index', compact('data', 'couriers'));
    }

    public function cityStore(Request $request)
    {
        $courier_name = Courier::find($request->courier_id)->courier_name;
        $input = array_merge($request->all(), [
            'courier_name' => $courier_name,
        ]);
        CourierCity::create($input);
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.courier.city')->with('success', 'Courier City Added Successfully');
        } elseif (Auth::guard('manager')->check()) {
            return redirect()->route('manager.courier.city')->with('success', 'Courier City Added Successfully');
        } else {
            return back()->with('warning', 'Something Went Wrong');
        }

    }

    public function cityUpdate(Request $request)
    {
        $courier_name = Courier::find($request->courier_id)->courier_name;
        $input = array_merge($request->all(), [
            'courier_name' => $courier_name,
        ]);

        CourierCity::find($request->id)->update($input);
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.courier.city')->with('success', 'Courier City Updated Successfully');
        } elseif (Auth::guard('manager')->check()) {
            return redirect()->route('manager.courier.city')->with('success', 'Courier City Updated Successfully');
        } else {
            return back()->with('warning', 'Something Went Wrong');
        }

    }

    public function cityDelete($id)
    {
        $has_courier_city = CourierZone::where('city_id', $id)->first();
        if ($has_courier_city) {
            return back()->with('warning', 'This City Already In Zone');
        } else {
            CourierZone::where('city_id', $id)->delete();
            CourierCity::find($id)->delete();
            return back()->with('success', 'Courier City Deleted Successfully');
        }

    }

    ////for zone
    public function zoneIndex()
    {
        $data = CourierZone::all();
        $couriers = Courier::pluck('courier_name', 'id');
        $courier_cities = CourierCity::pluck('city_name', 'id');
        return view('backEnd.admin.couriers.zones.index', compact('data', 'couriers', 'courier_cities'));
    }

    public function zoneStore(Request $request)
    {
        $courier_name = Courier::find($request->courier_id)->courier_name;
        $city_name = CourierCity::find($request->city_id)->city_name;
        $input = array_merge($request->all(), [
            'courier_name' => $courier_name,
            'city_name' => $city_name,
        ]);
        CourierZone::create($input);
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.courier.zone')->with('success', 'Courier Zone Added Successfully');
        } elseif (Auth::guard('manager')->check()) {
            return redirect()->route('manager.courier.zone')->with('success', 'Courier Zone Added Successfully');
        } else {
            return back()->with('warning', 'Something Went Wrong');
        }

    }

    public function zoneUpdate(Request $request)
    {
        $courier_name = Courier::find($request->courier_id)->courier_name;
        $city_name = CourierCity::find($request->city_id)->city_name;
        $input = array_merge($request->all(), [
            'courier_name' => $courier_name,
            'city_name' => $city_name,
        ]);

        CourierZone::find($request->id)->update($input);
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.courier.zone')->with('success', 'Courier Zone Updated Successfully');
        } elseif (Auth::guard('manager')->check()) {
            return redirect()->route('manager.courier.zone')->with('success', 'Courier Zone Updated Successfully');
        } else {
            return back()->with('warning', 'Something Went Wrong');
        }

    }

    public function zoneDelete($id)
    {
        CourierZone::find($id)->delete();
        return back()->with('success', 'Courier Zone Deleted Successfully');
    }

    public function ajaxGetCities(Request $request)
    {
        $data = CourierCity::where('courier_id', $request->id)->pluck('city_name', 'id');
        return response()->json($data);
    }

    public function pathaoAjaxGetCities(Request $request)
    {
        //dd($request->all());
        if ($request->id == 1) {
            /*$credential = DB::table('pathao_apis')->select('access_token')->where('id', 1)->first();
            $url = 'https://api-hermes.pathao.com/aladdin/api/v1/countries/1/city-list';
            $curl = curl_init();
            $headers = [
                'accept: application/json',
                'content-type: application/json',
                'Authorization: Bearer ' . $credential->access_token,
            ];
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, false);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $d = curl_exec($curl);
            $d = json_decode($d, true);
            curl_close($curl);

            $data = [];
            foreach ($d['data']['data'] as $key => $item) {
                $data[$item['city_id']] = $item['city_name'];
            }*/
            $data = DB::table('pathao_cities')->pluck('city_name', 'parent_id')->toArray();
            //dd($data);
            return response()->json($data);
        } else {
            $data = null;
            return response()->json($data);
        }

    }

    public function pathaoAjaxGetZones(Request $request)
    {
        try {
            /*$credential = DB::table('pathao_apis')->select('access_token')->where('id', 1)->first();
            //dd($credential);
            $url = 'https://api-hermes.pathao.com/aladdin/api/v1/cities/' . $request->id . '/zone-list';
            $curl = curl_init();
            $headers = [
                'accept: application/json',
                'content-type: application/json',
                'Authorization: Bearer ' . $credential->access_token,
            ];
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, false);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $d = curl_exec($curl);
            $d = json_decode($d, true);
            curl_close($curl);

            $data = [];
            foreach ($d['data']['data'] as $key => $item) {
                $data[$item['zone_id']] = $item['zone_name'];
            }*/
            $data = DB::table('pathao_zones')->where('city_id', $request->id)->pluck('zone_name', 'parent_id')->toArray();
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json($e);
        }

    }

    public function pathaoAddressParser(Request $request)
    {
        $credential = DB::table('pathao_apis')->select('access_token')->where('id', 1)->first();
        //dd($credential);
        $url = 'https://merchant.pathao.com/api/v1/address-parser';
        $headers = [
            'accept: application/json',
            'content-type: application/json',
            'Authorization: Bearer ' . $credential->access_token,
        ];
        $value = [
            'address' => $request->address
        ];
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>json_encode($value),
            CURLOPT_HTTPHEADER => $headers,
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return response()->json(json_decode($response));
    }

    public function redxAjaxGetCities(Request $request)
    {
        if ($request->id == 2) {
            $credential = DB::table('redx_apis')->select('is_active', 'access_token')->where('id', 1)->first();
            $url = 'https://openapi.redx.com.bd/v1.0.0-beta/areas';
            $curl = curl_init();
            $headers = [
                'API-ACCESS-TOKEN: Bearer ' . $credential->access_token,
            ];

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => $headers,
            ));

            $d1 = curl_exec($curl);
            $d1 = json_decode($d1, true)['areas'];
            curl_close($curl);

            $data = [];
            foreach ($d1 as $key => $item) {
                $data[$item['id']] = $item['division_name'] . ' > ' . $item['district_name'] . ' > ' . $item['name'];
            }
            return response()->json($data);
        } else {
            $data = null;
            return response()->json($data);
        }

    }

    public function ajaxGetZones(Request $request)
    {
        $data = CourierZone::where('city_id', $request->id)->pluck('zone_name', 'id');
        return response()->json($data);
    }

    public function ajaxGetCCharge(Request $request)
    {
        $data = Courier::find($request->id)->courier_charge;
        return response()->json($data);
    }


    public function carrybeeAjaxGetCities(Request $request)
    {
        //dd($request->all());
        if ($request->id == 4) {
            $credential = DB::table('carry_bee_apis')->select('access_token')->where('id', 1)->first();
            //dd($credential);
            $url = 'https://developers.carrybee.com/api/city-list';
            $curl = curl_init();
            $headers = [
                'accept: application/json',
                'content-type: application/json',
                'Authorization: Bearer ' . $credential->access_token,
            ];
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, false);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $d = curl_exec($curl);
            $d = json_decode($d, true);
            //dd($d['data']['data']);
            curl_close($curl);

            $data = [];
            foreach ($d['data']['data'] as $key => $item) {
                $data[$item['city_id']] = $item['city_name'];
            }
            return response()->json($data);
        } else {
            $data = null;
            return response()->json($data);
        }

    }

    public function carrybeeAjaxGetZones(Request $request)
    {
        try {
            $credential = DB::table('carry_bee_apis')->select('access_token')->where('id', 1)->first();
            //dd($credential);
            $url = 'https://developers.carrybee.com/api/cities/' . $request->id . '/zones';
            $curl = curl_init();
            $headers = [
                'accept: application/json',
                'content-type: application/json',
                'Authorization: Bearer ' . $credential->access_token,
            ];
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, false);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $d = curl_exec($curl);
            $d = json_decode($d, true);
            curl_close($curl);

            $data = [];
            foreach ($d['data']['data'] as $key => $item) {
                $data[$item['zone_id']] = $item['zone_name'];
            }

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json($e);
        }

    }
}
