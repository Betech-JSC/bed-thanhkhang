<?php

return [
    /*
    |--------------------------------------------------------------------------
    | PayPal Payment Gateway Configuration
    |--------------------------------------------------------------------------
    */

    'mode' => env('PAYPAL_MODE', 'sandbox'), // 'sandbox' hoặc 'live'

    'sandbox' => [
        'client_id'     => env('PAYPAL_SANDBOX_CLIENT_ID', ''),
        'client_secret' => env('PAYPAL_SANDBOX_CLIENT_SECRET', ''),
    ],

    'live' => [
        'client_id'     => env('PAYPAL_LIVE_CLIENT_ID', ''),
        'client_secret' => env('PAYPAL_LIVE_CLIENT_SECRET', ''),
    ],

    'currency'      => env('PAYPAL_CURRENCY', 'USD'),
    'exchange_rate' => (int) env('PAYPAL_EXCHANGE_RATE', 25000), // 1 USD = 25,000 VND
];
