<?php

namespace App\Http\Controllers\BackEnd;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\SmsSetting;
use App\Models\WebSettings;
use Illuminate\Http\Request;

class SmsSettingsController extends Controller
{
    // index
    public function indexNumber()
    {
        $smsSettings = $this->ensureSettings();
        $webSettings = WebSettings::find(1);

        return view('backEnd.admin.sms.number', compact('smsSettings', 'webSettings'));
    }

    public function updateNumber(Request $request)
    {
        $smsSettingsInput = $request->input('smsSettings', []);
        $this->updateSettings($smsSettingsInput, false);

        // Update global sms settings
        $webSettings = WebSettings::find(1);
        if ($webSettings) {
            $webSettings->update([
                'is_sms_enabled' => $request->has('is_sms_enabled'),
                'sms_start_time' => $request->input('sms_start_time'),
                'sms_end_time' => $request->input('sms_end_time'),
            ]);
        }

        return back()->with('success', 'SMS settings updated successfully!');
    }

    public function indexWhatsapp()
    {
        $smsSettings = $this->ensureSettings();
        $webSettings = WebSettings::find(1);

        return view('backEnd.admin.sms.whatsapp', compact('smsSettings', 'webSettings'));
    }

    public function updateWhatsapp(Request $request)
    {
        $smsSettingsInput = $request->input('smsSettings', []);

        $this->updateSettings($smsSettingsInput, true);

        // Update global sms settings
        $webSettings = WebSettings::find(1);
        if ($webSettings) {
            $webSettings->update([
                'is_sms_enabled' => $request->has('is_sms_enabled'),
                'sms_start_time' => $request->input('sms_start_time'),
                'sms_end_time' => $request->input('sms_end_time'),
            ]);
        }

        return back()->with('success', 'WhatsApp settings updated successfully!');
    }

    private function ensureSettings(): array
    {
        $smsSettings = [];

        foreach (OrderStatus::labelsToValues() as $label => $numeric) {
            $smsSettings[$numeric] = SmsSetting::firstOrCreate(
                ['status' => $numeric],
                [
                    'name' => $label,
                    'status' => $numeric,
                    'is_active' => false,
                    'is_whatsapp' => 0,
                    'message' => '',
                    'template_name' => '',
                ]
            );
        }

        return $smsSettings;
    }

    private function updateSettings(array $input, bool $isWhatsapp): void
    {
        foreach ($input as $numeric => $data) {
            $setting = SmsSetting::where('status', $numeric)->first();
            if (! $setting) {
                continue;
            }

            $payload = $isWhatsapp
                ? [
                    'is_whatsapp' => isset($data['wp_active']) ? 1 : 0,
                    'template_name' => $data['template_name'] ?? '',
                ]
                : [
                    'is_active' => isset($data['active']) ? 1 : 0,
                    'message' => $data['message'] ?? '',
                ];

            $setting->update($payload);
        }
    }
}
