<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    //    'allowed_methods' => ['*'],
    'allowed_methods' => ['PUT', 'DELETE', 'OPTIONS'],

    //'allowed_origins' => ['*'],
    'allowed_origins' => [
        'http://localhost:5173',
        'http://aarushi.net:5173',
        'http://192.168.1.11:5173'
    ],

    //'allowed_origins_patterns' => [],
    'allowed_origins_patterns' => ['*localhost*', '*aarushi.net*'],

    //'allowed_headers' => ['*'],
    'allowed_headers' => ['Content-Type', 'Authorization', 'X-CSRF-TOKEN'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
