<?php

namespace App\Http\Controllers;

use App\CarryBeeApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CarryBeeApiSettingsController extends Controller
{
    public function index()
    {
        $data = CarryBeeApi::find(1);
        return view('backEnd.admin.carrybee_api_settings', compact('data'));
    }

    public function update(Request $request)
    {
        try {
            if ($request->is_active) {
                $is_active = 1;
            } else {
                $is_active = 0;
            }

            $input = array_merge($request->all(), [
                'is_active' => $is_active
            ]);

            CarryBeeApi::find(1)->update($input);
            return redirect()->back()->with('success', 'CarryBee API Settings Updated Successfully');
        } catch (\Exception $e) {
            dd($e);
            return redirect()->back()->with('error', $e);
        }
    }

    public function generateAccessToken(Request $request)
    {
        $credential = DB::table('carry_bee_apis')->select('email', 'password')->where('id', 1)->first();
        //dd($credential);
        $url = 'https://developers.carrybee.com/api/login';
        $curl = curl_init();
        $vars = [
            'email' => $credential->email,
            'password' => $credential->password,
            'grant_type' => 'password',
        ];
        $headers = [
            'accept: application/json',
            'content-type: application/json',
        ];
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

        CarryBeeApi::find(1)->update([
            'access_token' => $data['data']['token']
        ]);

        return back()->with('success', 'New Access Token Generated Successfully');
    }
}
