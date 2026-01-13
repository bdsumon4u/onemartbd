<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Models\PathaoApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PathaoApiSettingsController extends Controller
{
    public function index()
    {
        $data = PathaoApi::find(1);

        return view('backEnd.admin.pathao_api_settings', compact('data'));
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
                'is_active' => $is_active,
            ]);

            PathaoApi::find(1)->update($input);

            return back()->with('success', 'Pathao API Settings Updated Successfully');
        } catch (\Exception $e) {
            dd($e);

            return back()->with('error', $e);
        }
    }

    public function generateAccessToken(Request $request)
    {
        $credential = DB::table('pathao_apis')->select('client_id', 'client_secret', 'username', 'password')->where('id', 1)->first();
        // dd($credential);
        $url = 'https://api-hermes.pathao.com/aladdin/api/v1/issue-token';
        $curl = curl_init();
        $vars = [
            'client_id' => $credential->client_id,
            'client_secret' => $credential->client_secret,
            'username' => $credential->username,
            'password' => $credential->password,
            'grant_type' => 'password',
        ];
        $headers = [
            'accept: application/json',
            'content-type: application/json',
        ];
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

        PathaoApi::find(1)->update([
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'],
        ]);

        return back()->with('success', 'New Access Token Generated Successfully');
    }
}
