<?php

return [
    'app_name' => 'Digital Ideas',
    'site_url' => 'https://www.digitalideasltd.in/',
    'timezone' => 'UTC',
    'locale' => 'en',

    'session' => [
        'domain' => 'www.digitalideasltd.in',
        'cookie' => 'session_digitalideasltd',
        'lifetime' => 120,
    ],

    'assets' => [
        'path' => 'assets/www.digitalideasltd.in',
    ],

    'iframe' => [
        'url' => env('GARAGE_IFRAME_URL', 'https://www.digitalideasltd.co.uk/'),
        'height' => env('GARAGE_IFRAME_HEIGHT', '600px'),
    ],
];
