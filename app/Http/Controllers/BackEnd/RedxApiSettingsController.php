<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateRedxApiSettingsRequest;
use App\Models\RedxApi;

class RedxApiSettingsController extends Controller
{
    public function index()
    {
        $data = $this->settings();

        return view('backEnd.admin.redx_api_settings', compact('data'));
    }

    public function update(UpdateRedxApiSettingsRequest $request)
    {
        try {
            $this->settings()->update($request->validated());

            return back()->with('success', 'RedX API Settings Updated Successfully');
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Something went wrong while updating RedX API settings.');
        }
    }

    private function settings(): RedxApi
    {
        $settings = RedxApi::query()->find(1);

        if ($settings) {
            return $settings;
        }

        $settings = new RedxApi;
        $settings->id = 1;
        $settings->access_token = '';
        $settings->is_active = false;
        $settings->save();

        return $settings;
    }
}
