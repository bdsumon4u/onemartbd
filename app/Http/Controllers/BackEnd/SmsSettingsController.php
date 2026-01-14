<?php

namespace App\Http\Controllers\BackEnd;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\SmsSetting;
use Illuminate\Http\Request;

class SmsSettingsController extends Controller
{
    // index
    public function indexNumber()
    {
        $smsSettings = $this->ensureSettings();

        return view('backEnd.admin.sms.number', compact('smsSettings'));
    }

    public function updateNumber(Request $request)
    {
        $smsSettingsInput = $request->input('smsSettings', []);
        $this->updateSettings($smsSettingsInput, false);

        return back()->with('success', 'SMS settings updated successfully!');
    }

    public function indexWhatsapp()
    {
        $smsSettings = $this->ensureSettings();

        return view('backEnd.admin.sms.whatsapp', compact('smsSettings'));
    }

    public function updateWhatsapp(Request $request)
    {
        $smsSettingsInput = $request->input('smsSettings', []);

        $this->updateSettings($smsSettingsInput, true);

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
