<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'call_automation' => [
        'api_key' => env('CALL_AUTOMATION_API_KEY', ''),
        'did' => env('CALL_AUTOMATION_DID', '09643301133'),
        'maintext' => env('CALL_AUTOMATION_MAIN_TEXT', 'Hello dear customer, this is a confirmation call regarding your recent order with us. Please press 1 to confirm your order or press 2 to cancel your order.'),
        'text1' => env('CALL_AUTOMATION_TEXT1', 'Thanks For Pressing 1'),
        'text2' => env('CALL_AUTOMATION_TEXT2', 'Thanks For Pressing 2'),
        'call_url' => env('CALL_AUTOMATION_CALL_URL', 'https://ccs.teamitqan.com/api/MakeTextCall/Call'),
        'retry_url' => env('CALL_AUTOMATION_RETRY_URL', 'https://ccs.teamitqan.com/api/MakeTextCall/tts_a_retry'),
        'check_response_url' => env('CALL_AUTOMATION_CHECK_RESPONSE_URL', 'https://ccs.teamitqan.com/api/MakeTextCall/CheckResponse'),
    ],

];
