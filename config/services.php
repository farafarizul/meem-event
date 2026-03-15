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
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'meem' => [
        'base_url'          => env('MEEM_API_BASE_URL', 'https://meem.com.my/api/v1'),
        'gold_price_token'  => env('MEEM_GOLD_PRICE_TOKEN', '3173|aPsrnIGfi9BuOoMPRVMJA9EhQVBIQ3FCOwtJaiim700007aa'),
        'silver_price_token' => env('MEEM_SILVER_PRICE_TOKEN', '3173|aPsrnIGfi9BuOoMPRVMJA9EhQVBIQ3FCOwtJaiim700007aa'),
    ],

    'groq' => [
        'api_key' => env('GROQ_API_KEY'),
        'api_url' => env('GROQ_API_URL', 'https://api.groq.com/openai/v1/responses'),
        'model'   => env('GROQ_MODEL', 'moonshotai/kimi-k2-instruct-0905'),
    ],

    'onesignal' => [
        'app_id'       => env('ONESIGNAL_APP_ID', '65a129e2-aed4-49eb-b00a-9f07a5c05ba5'),
        'rest_api_key' => env('ONESIGNAL_REST_API_KEY', 'os_v2_app_mwqstyvo2re6xmakt4d2lqc3uwv7zqyy24oue4edbejuoeva22ykgjccaysdujo4ox5vo2ahpy6in3yp6otaihborsj46xdz27p6rxy'),
    ],

];
