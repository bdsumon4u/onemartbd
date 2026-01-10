<?php

namespace App\Http\Controllers;

use App\RedxApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RedxApiSettingsController extends Controller
{
    public function index()
    {
        $data = RedxApi::find(1);
        return view('backEnd.admin.redx_api_settings', compact('data'));
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

            RedxApi::find(1)->update($input);
            return back()->with('success', 'RedX API Settings Updated Successfully');
        } catch (\Exception $e) {
            dd($e);
            return back()->with('error', $e);
        }
    }
}
