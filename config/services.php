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
        'domain' => env('MAILGUN_DOMAIN', 'cartxter.com'),
        'secret' => env('MAILGUN_SECRET', '731694d0e6db4f1075b12be8c5ca57e8-1053eade-dab9c59e'),
    ],

    'mandrill' => [
        'secret' => env('MANDRILL_SECRET', 'vif_V562pDtYkZxXLM0Y2g'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'product' => env('STRIPE_PRODUCT'),
    ],
];
