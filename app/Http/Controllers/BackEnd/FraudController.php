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
                CURLOPT_URL => 'https://courierrank.com/api/get-customer-details/'.$order->customer_phone,
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

            // dd('Token: ' . env('TJ_FC_API') . ' Response:' . $response);
            curl_close($curl);

            if (json_decode($response)) {
                $data = [
                    'pathao_delivered' => json_decode($response)->pathao_delivered,
                    'pathao_returned' => json_decode($response)->pathao_returned,
                    'steadfast_delivered' => json_decode($response)->steadfast_delivered,
                    'steadfast_returned' => json_decode($response)->steadfast_returned,
                    'redx_delivered' => json_decode($response)->redx_delivered,
                    'redx_returned' => json_decode($response)->redx_returned,
                ];
                $data += [
                    'total_delivered' => $data['pathao_delivered'] + $data['steadfast_delivered'] + $data['redx_delivered'],
                    'total_returned' => $data['pathao_returned'] + $data['steadfast_returned'] + $data['redx_returned'],
                ];
                $data += [
                    'total' => $data['total_delivered'] + $data['total_returned'],
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
