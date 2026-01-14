<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePageSettingsRequest;
use App\Models\PageSetting;

class PageSettingsController extends Controller
{
    public function index()
    {
        $data = $this->settings();

        return view('backEnd.admin.page_settings', compact('data'));
    }

    public function update(UpdatePageSettingsRequest $request)
    {
        try {
            $this->settings()->update($request->validated());

            return back()->with('success', 'Page Settings Updated Successfully');
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Something went wrong while updating page settings.');
        }
    }

    private function settings(): PageSetting
    {
        $settings = PageSetting::query()->find(1);

        if ($settings) {
            return $settings;
        }

        $settings = new PageSetting;
        $settings->id = 1;
        $settings->about_us = '';
        $settings->delivery_policy = '';
        $settings->return_policy = '';
        $settings->save();

        return $settings;
    }
}
