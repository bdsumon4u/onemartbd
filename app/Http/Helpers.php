<?php

use App\OrderTransaction;

if (! function_exists('order_transaction')) {
    function order_transaction($type, $order_id, $text, $comment, $created_by, $crated_by_id, $assigned_to)
    {
        OrderTransaction::create([
            'type' => $type,
            'order_id' => $order_id,
            'text' => $text,
            'comment' => $comment,
            'created_by' => $created_by,
            'created_by_id' => $crated_by_id,
            'assigned_to' => $assigned_to,
        ]);
    }
}

if (! function_exists('api_call')) {
    function api_call($url, $method, $data)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => [
                'accept: application/json',
                'content-type: application/json',
            ],
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $data,
        ]);

        $response = curl_exec($curl);
        $response = json_decode($response);
        // dd($response);
        curl_close($curl);

        return $response;
    }
}
