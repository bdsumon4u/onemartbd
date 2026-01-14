<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use App\Models\CourierCity;
use App\Models\CourierZone;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CourierController extends Controller
{
    public function index()
    {
        return view('backEnd.admin.couriers.index', [
            'data' => Courier::all(),
        ]);
    }

    public function store(Request $request)
    {
        Courier::create($this->prepareCourierData($request));

        return $this->redirectBack('courier', 'Courier Added Successfully');
    }

    public function update(Request $request)
    {
        Courier::find($request->id)->update($this->prepareCourierData($request));

        return $this->redirectBack('courier', 'Courier Updated Successfully');
    }

    public function delete($id)
    {
        if (Order::where('courier_id', $id)->exists()) {
            return back()->with('warning', 'This Product Already In Order');
        }

        CourierCity::where('courier_id', $id)->delete();
        CourierZone::where('courier_id', $id)->delete();
        Courier::find($id)->delete();

        return back()->with('success', 'Courier Deleted Successfully');
    }

    public function cityIndex()
    {
        return view('backEnd.admin.couriers.cities.index', [
            'data' => CourierCity::all(),
            'couriers' => Courier::pluck('courier_name', 'id'),
        ]);
    }

    public function cityStore(Request $request)
    {
        CourierCity::create($this->prepareCityData($request));

        return $this->redirectBack('courier.city', 'Courier City Added Successfully');
    }

    public function cityUpdate(Request $request)
    {
        CourierCity::find($request->id)->update($this->prepareCityData($request));

        return $this->redirectBack('courier.city', 'Courier City Updated Successfully');
    }

    public function cityDelete($id)
    {
        if (CourierZone::where('city_id', $id)->exists()) {
            return back()->with('warning', 'This City Already In Zone');
        }

        CourierZone::where('city_id', $id)->delete();
        CourierCity::find($id)->delete();

        return back()->with('success', 'Courier City Deleted Successfully');
    }

    public function zoneIndex()
    {
        return view('backEnd.admin.couriers.zones.index', [
            'data' => CourierZone::all(),
            'couriers' => Courier::pluck('courier_name', 'id'),
            'courier_cities' => CourierCity::pluck('city_name', 'id'),
        ]);
    }

    public function zoneStore(Request $request)
    {
        CourierZone::create($this->prepareZoneData($request));

        return $this->redirectBack('courier.zone', 'Courier Zone Added Successfully');
    }

    public function zoneUpdate(Request $request)
    {
        CourierZone::find($request->id)->update($this->prepareZoneData($request));

        return $this->redirectBack('courier.zone', 'Courier Zone Updated Successfully');
    }

    public function zoneDelete($id)
    {
        CourierZone::find($id)->delete();

        return back()->with('success', 'Courier Zone Deleted Successfully');
    }

    public function ajaxGetCities(Request $request)
    {
        return response()->json(
            CourierCity::where('courier_id', $request->id)->pluck('city_name', 'id')
        );
    }

    public function pathaoAjaxGetCities(Request $request)
    {
        if (! $this->isCourierValid($request->id, 1)) {
            return response()->json(null);
        }

        return response()->json(
            DB::table('pathao_cities')->pluck('city_name', 'parent_id')
        );
    }

    public function pathaoAjaxGetZones(Request $request)
    {
        return response()->json(
            DB::table('pathao_zones')
                ->where('city_id', $request->id)
                ->pluck('zone_name', 'parent_id')
        );
    }

    public function pathaoAddressParser(Request $request)
    {
        $token = DB::table('pathao_apis')->value('access_token');
        $response = $this->makeCurlRequest(
            'https://merchant.pathao.com/api/v1/address-parser',
            $token,
            'POST',
            ['address' => $request->address]
        );

        return response()->json(json_decode($response));
    }

    public function redxAjaxGetCities(Request $request)
    {
        if (! $this->isCourierValid($request->id, 2)) {
            return response()->json(null);
        }

        $token = DB::table('redx_apis')->value('access_token');
        $response = $this->makeCurlRequest(
            'https://openapi.redx.com.bd/v1.0.0-beta/areas',
            $token,
            'GET',
            [],
            'API-ACCESS-TOKEN'
        );
        $areas = json_decode($response, true)['areas'] ?? [];

        $data = collect($areas)->mapWithKeys(fn ($item) => [
            $item['id'] => "{$item['division_name']} > {$item['district_name']} > {$item['name']}",
        ]);

        return response()->json($data);
    }

    public function ajaxGetZones(Request $request)
    {
        return response()->json(
            CourierZone::where('city_id', $request->id)->pluck('zone_name', 'id')
        );
    }

    public function ajaxGetCCharge(Request $request)
    {
        return response()->json(
            Courier::find($request->id)->courier_charge
        );
    }

    public function carrybeeAjaxGetCities(Request $request)
    {
        if (! $this->isCourierValid($request->id, 4)) {
            return response()->json(null);
        }

        $token = DB::table('carry_bee_apis')->value('access_token');
        $response = $this->makeCurlRequest('https://developers.carrybee.com/api/city-list', $token);
        $cities = json_decode($response, true)['data']['data'] ?? [];

        return response()->json(collect($cities)->pluck('city_name', 'city_id'));
    }

    public function carrybeeAjaxGetZones(Request $request)
    {
        $token = DB::table('carry_bee_apis')->value('access_token');
        $response = $this->makeCurlRequest(
            "https://developers.carrybee.com/api/cities/{$request->id}/zones",
            $token
        );
        $zones = json_decode($response, true)['data']['data'] ?? [];

        return response()->json(collect($zones)->pluck('zone_name', 'zone_id'));
    }

    private function prepareCourierData(Request $request): array
    {
        return array_merge($request->all(), [
            'is_city' => $request->is_city === 'on' ? 1 : 0,
            'is_zone' => $request->is_zone === 'on' ? 1 : 0,
        ]);
    }

    private function prepareCityData(Request $request): array
    {
        return array_merge($request->all(), [
            'courier_name' => Courier::find($request->courier_id)->courier_name,
        ]);
    }

    private function prepareZoneData(Request $request): array
    {
        return array_merge($request->all(), [
            'courier_name' => Courier::find($request->courier_id)->courier_name,
            'city_name' => CourierCity::find($request->city_id)->city_name,
        ]);
    }

    private function isCourierValid(int $courierId, int $expectedId): bool
    {
        return $courierId === $expectedId;
    }

    private function redirectBack(string $route, string $message)
    {
        if (Auth::guard('admin')->check()) {
            return to_route("admin.{$route}")->with('success', $message);
        }

        if (Auth::guard('manager')->check()) {
            return to_route("manager.{$route}")->with('success', $message);
        }

        return back()->with('warning', 'Something Went Wrong');
    }

    private function makeCurlRequest(string $url, string $token, string $method = 'GET', array $data = [], string $authHeader = 'Authorization'): string
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $data ? json_encode($data) : null,
            CURLOPT_HTTPHEADER => [
                'accept: application/json',
                'content-type: application/json',
                "{$authHeader}: Bearer {$token}",
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }
}
