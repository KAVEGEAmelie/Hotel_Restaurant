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
        'base_url' => env('CASHPAY_BASE_URL', 'https://sandbox.semoa-payments.com/api'),
        'username' => env('CASHPAY_USERNAME'),
        'password' => env('CASHPAY_PASSWORD'),
        'client_id' => env('CASHPAY_CLIENT_ID'),
        'client_secret' => env('CASHPAY_CLIENT_SECRET'),
        'api_reference' => env('CASHPAY_API_REFERENCE'),
        'api_key' => env('CASHPAY_API_KEY'),
        'currency' => env('CASHPAY_CURRENCY', 'XOF'),
        'terminal_code' => env('CASHPAY_TERMINAL_CODE'),
    ],

    'cinetpay' => [
        'api_key' => env('CINETPAY_API_KEY'),
        'site_id' => env('CINETPAY_SITE_ID'),
        'secret_key' => env('CINETPAY_SECRET_KEY'),
        'currency' => env('CINETPAY_CURRENCY', 'XOF'),
        'environment' => env('CINETPAY_ENVIRONMENT', 'sandbox'), // sandbox ou production
    ],

];
