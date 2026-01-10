<?php

namespace App\Http\Controllers;

use App\SteadFastApi;
use Illuminate\Http\Request;

class SteadFastApiSettingsController extends Controller
{
    public function index()
    {
        $data = SteadFastApi::find(1);
        return view('backEnd.admin.stead_fast_api_settings', compact('data'));
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

            SteadFastApi::find(1)->update($input);
            return back()->with('success', 'Stead Fast API Settings Updated Successfully');
        } catch (\Exception $e) {
            dd($e);
            return back()->with('error', $e);
        }
    }
}
