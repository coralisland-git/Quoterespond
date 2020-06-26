<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default api
    |--------------------------------------------------------------------------
    |
    | Supported: 'google', 'bitly', 'rebrandly'
    |
    */
    'driver' => env('SHORTLINK_DRIVER', 'google'),

    /*
    |--------------------------------------------------------------------------
    | Google api
    |--------------------------------------------------------------------------
    |
    | This is Google shortener api url and key using for aplication.
    |
    */
    'google' => [
        'url' => env('SHORTLINK_GOOGLE_URL', 'https://www.googleapis.com/urlshortener/v1'),
        'key' => env('SHORTLINK_GOOGLE_KEY', 'your_google_api_key'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Bitly api
    |--------------------------------------------------------------------------
    |
    | This is Bitly shortener api url and key using for aplication.
    |
    */
    'bitly' => [
        'url' => env('SHORTLINK_BITLY_URL', 'https://api-ssl.bitly.com/v4'),
        //'key' => env('SHORTLINK_BITLY_KEY', 'f24f4a36a663e8c3b2ed4a9a612a8075258ac0e2'), /* info@div-art.com || urielturk@gmail.com */
        'key' => env('SHORTLINK_BITLY_KEY', '9565d008ca3c0ad4d2b52519f3e50377deb4ad1f'), /* uri@contractortexter.com */
        //'key' => env('SHORTLINK_BITLY_KEY', '20a0db749b1df82bf8896c160587e58bc8b196cf'), /* dd.divart@gmail.com || uri@contractortexter.me */
    ],

    /*
    |--------------------------------------------------------------------------
    | Rebrandly api
    |--------------------------------------------------------------------------
    |
    | This is Rebrandly shortener api url and key using for aplication.
    |
    */
    'rebrandly' => [
        'url' => env('SHORTLINK_REBRANDLY_URL', 'https://api.rebrandly.com/v1'),
        'key' => env('SHORTLINK_REBRANDLY_KEY', 'your_rebrandly_api_key'),
    ],


];