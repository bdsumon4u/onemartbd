<?php


namespace App;


use Illuminate\Support\Facades\DB;

class ConversionAPI
{
    private $access_token;

    function __construct()
    {
        $settings = DB::table('web_settings')->where('id', 1)->first();
        $this->access_token = $settings->fb_cpi_access_token;
    }

    function post_request($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $this->access_token, 'Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        curl_close($ch);
        //$serverReponseObject = json_decode($server_output);

        // Debug
        //print_r($serverReponseObject);
        //dd($serverReponseObject);
    }
}
