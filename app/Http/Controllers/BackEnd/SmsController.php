<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Services\BanglaToEnglishConverter;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    public function sendSms(Request $request)
    {
        $apikey = config('app.sms_api_key');

        $msisdn = ltrim((string) BanglaToEnglishConverter::bn2en($request->customer_phone), '+');
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.sms.net.bd/sendsms',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => ['api_key' => $apikey, 'msg' => $request->sms_body, 'to' => $msisdn],
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        if (json_decode($response, true)['error'] == 0) {
            return response()->json(['success' => 'SMS Sent Successfully']);
        } else {
            return response()->json(['error' => json_decode($response, true)['msg']]);
        }
    }
}
