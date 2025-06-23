<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => env('SES_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],
    // config/services.php

    'ukvehicle' => [
        'api_key' => env('UK_VEHICLE_API_KEY'),
    ],
    'api_secure_token' => env('API_SECURE_TOKEN'),
    'bond' => [
        'api_mode' => env('BOND_API_MODE', 'test'), // 'live' or 'test'
        'api_code' => env('BOND_API_CODE'),
        'trading_point' => env('BOND_TRADING_POINT'),
        'supplier_email' => env('BOND_SUPPLIER_EMAIL'),
    ],

    'verifalia' => [
        'username' => env('VERIFALIA_USERNAME'),
        'password' => env('VERIFALIA_PASSWORD'),
    ],

    'stripe' => [
        'model' => App\Models\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook' => [
            'secret' => env('STRIPE_WEBHOOK_SECRET'),
            'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
        ],
    ],

];
