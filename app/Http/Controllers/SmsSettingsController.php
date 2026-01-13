<?php

namespace App\Http\Controllers;

use App\Models\SmsSetting;
use Illuminate\Http\Request;

class SmsSettingsController extends Controller
{
    // index
    public function indexNumber()
    {
        $statusMapping = [
            'Hold' => 0,
            'Delivered' => 1,
            'Processing' => 2,
            'Pending Payment' => 3,
            'Cancelled' => 4,
            'Pending Invoice' => 5,
            'On Delivery' => 6,
            'Pending Return' => 7,
            'Courier' => 8,
            'No Response' => 9,
            'Invoiced' => 10,
            'Return' => 11,
            'Incomplete' => 12,
            'Confirmed' => 13,
            'Stock Out' => 14,
            'Partial Delivery' => 15,
            'Lost' => 16,
        ];

        $smsSettings = [];
        foreach ($statusMapping as $label => $numeric) {
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

        return view('backEnd.admin.sms.number', compact('smsSettings'));
    }

    public function updateNumber(Request $request)
    {
        $smsSettingsInput = $request->input('smsSettings', []);
        // dd($smsSettingsInput);

        foreach ($smsSettingsInput as $numeric => $data) {
            $setting = SmsSetting::where('status', $numeric)->first();

            if ($setting) {
                $setting->update([
                    'is_active' => isset($data['active']) ? 1 : 0,
                    'message' => $data['message'] ?? '',
                ]);
            }
        }

        return back()->with('success', 'SMS settings updated successfully!');
    }

    public function indexWhatsapp()
    {
        $statusMapping = [
            'Hold' => 0,
            'Delivered' => 1,
            'Processing' => 2,
            'Pending Payment' => 3,
            'Cancelled' => 4,
            'Pending Invoice' => 5,
            'On Delivery' => 6,
            'Pending Return' => 7,
            'Courier' => 8,
            'No Response' => 9,
            'Invoiced' => 10,
            'Return' => 11,
            'Incomplete' => 12,
            'Confirmed' => 13,
            'Stock Out' => 14,
            'Partial Delivery' => 15,
            'Lost' => 16,
        ];

        $smsSettings = [];
        foreach ($statusMapping as $label => $numeric) {
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

        return view('backEnd.admin.sms.whatsapp', compact('smsSettings'));
    }

    public function updateWhatsapp(Request $request)
    {
        $smsSettingsInput = $request->input('smsSettings', []);

        // dd($smsSettingsInput);

        foreach ($smsSettingsInput as $numeric => $data) {
            $setting = SmsSetting::where('status', $numeric)->first();

            if ($setting) {
                $setting->update([
                    'is_whatsapp' => isset($data['wp_active']) ? 1 : 0,
                    'template_name' => $data['template_name'] ?? '',
                ]);
            }
        }

        return back()->with('success', 'WhatsApp settings updated successfully!');
    }
}
