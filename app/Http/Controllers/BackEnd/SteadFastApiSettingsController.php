<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Models\SteadFastApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
            SteadFastApi::find(1)?->update(array_merge($request->all(), [
                'is_active' => $request->boolean('is_active'),
            ]));

            return back()->with('success', 'Stead Fast API Settings Updated Successfully');
        } catch (\Exception $e) {
            Log::error('SteadFast API settings update failed', ['error' => $e->getMessage()]);

            return back()->with('error', 'Something went wrong while updating settings');
        }
    }
}
