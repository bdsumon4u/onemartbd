<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Models\CallAutomationSetting;
use Illuminate\Http\Request;

class CallAutomationSettingsController extends Controller
{
    public function edit()
    {
        $settings = CallAutomationSetting::first();

        return view('backEnd.admin.call_automation_settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'api_key' => 'nullable|string',
            'did' => 'nullable|string',
            'maintext' => 'nullable|string',
            'text1' => 'nullable|string',
            'text2' => 'nullable|string',
            'call_url' => 'nullable|url',
            'retry_url' => 'nullable|url',
            'check_response_url' => 'nullable|url',
        ]);

        $settings = CallAutomationSetting::first();

        if (! $settings) {
            CallAutomationSetting::create($data);
        } else {
            $settings->update($data);
        }

        return redirect()->route('admin.call-automation.edit')->with('success', 'Call automation settings updated.');
    }
}
