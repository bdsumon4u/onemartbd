<?php

namespace App\Http\Controllers\BackEnd;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FraudController extends Controller
{
    public function fakeRemove($id)
    {
        DB::table('orders')->where('id', $id)->update([
            'is_fake' => 0,
        ]);

        return back()->with('success', 'Removed Successfully');
    }

    public function fraudCheck(Request $request, $id)
    {
        $order = Order::select('id', 'status', 'customer_phone', 'customer_activity')->find($id);

        if (strlen((string) $order->customer_phone) == 11) {
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://bdcourier.com/api/courier-check?phone='.$order->customer_phone,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer '.env('TJ_FC_API'),
                ],
            ]);

            $response = curl_exec($curl);

            curl_close($curl);

            if (json_decode($response) && json_decode($response)->status == 'success') {
                $data = [
                    'total' => json_decode($response)->courierData->summary->total_parcel,
                    'total_delivered' => json_decode($response)->courierData->summary->success_parcel,
                    'total_returned' => json_decode($response)->courierData->summary->cancelled_parcel,
                    'pathao_delivered' => json_decode($response)->courierData->pathao->success_parcel,
                    'pathao_returned' => json_decode($response)->courierData->pathao->cancelled_parcel,
                    'steadfast_delivered' => json_decode($response)->courierData->steadfast->success_parcel,
                    'steadfast_returned' => json_decode($response)->courierData->steadfast->cancelled_parcel,
                    'redx_delivered' => json_decode($response)->courierData->redx->success_parcel,
                    'redx_returned' => json_decode($response)->courierData->redx->cancelled_parcel,
                ];

                $order->update([
                    'customer_activity' => json_encode($data),
                ]);

                return back()->with('success', 'Activity Updated Successfully');
            } else {
                return back()->with('error', 'Something went wrong');
            }
        } else {
            return back()->with('warning', 'Phone number is not 11 digit');
        }
    }
}
