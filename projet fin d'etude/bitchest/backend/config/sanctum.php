<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sanctum Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration controls the Sanctum authentication features for
    | your application. Sanctum provides a lightweight API token and session
    | authentication solution for SPAs and mobile applications.
    |
    */

    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', 'localhost,127.0.0.1:8000,localhost:3000')),

    'expiration' => env('SANCTUM_EXPIRATION', 43200),

    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', ''),

    'middleware' => [
        'verify_csrf_token' => App\Http\Middleware\VerifyCsrfToken::class,
        'encrypt_cookies' => App\Http\Middleware\EncryptCookies::class,
    ],

];
