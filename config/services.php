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

];
