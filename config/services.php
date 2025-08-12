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

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

'cashpay' => [
    'url' => env('CASHPAY_API_URL', 'https://sandbox.semoa-payments.com/api'),
    'username' => env('CASHPAY_USERNAME', 'api_cashpay.leprintemps'),
    'password' => env('CASHPAY_PASSWORD', 'nwDT7XCQzU'),
    'client_id' => env('CASHPAY_CLIENT_ID', 'api_cashpay.leprintemps'),
    'client_secret' => env('CASHPAY_CLIENT_SECRET', 'NZLJv4V4A9R5zAN6oeQxFq985F9E2RxC'),
    'api_key' => env('CASHPAY_API_KEY', '2xiUFAWYSriHvSFYcv9YRl816uzgbC2Vth5g'),
    'api_reference' => env('CASHPAY_API_REFERENCE', '104'),
    'simulation_mode' => env('CASHPAY_SIMULATION_MODE', false),
],

// CinetPay Configuration
'cinetpay' => [
    'api_key' => env('CINETPAY_API_KEY'),
    'site_id' => env('CINETPAY_SITE_ID'),
    'secret_key' => env('CINETPAY_SECRET_KEY'),
    'currency' => env('CINETPAY_CURRENCY', 'XOF'),
    'environment' => env('CINETPAY_ENVIRONMENT', 'sandbox'),
    'webhook_url' => env('APP_URL') . '/webhook/cinetpay',
],

];
