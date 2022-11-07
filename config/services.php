<?php


/**
 * File name: services.php
 * Last modified: 2020.06.11 at 16:03:23
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 */

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
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => env('SES_REGION', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\Models\User::class,
    ],

    'facebook' => [
        'client_id' => '1209561842962012',         // Your Facebook Client ID
        'client_secret' => '46352a27e697a963ae10853955658da4', // Your Facebook Client Secret
        'redirect' => 'https://fantastic.sc2hrpt7736.universe.wf/public/login/facebook/callback',
    ],

    'google' => [
        'client_id' => '495519962172-0fpsfvj4lrvv74frveo9pfbg1o731uvh.apps.googleusercontent.com',         // Your google Client ID
        'client_secret' => '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', // Your google Client Secret
        'redirect' => 'https://fantastic.sc2hrpt7736.universe.wf/public/login/google/callback',
        // 'redirect' => 'http://127.0.0.1:8000/login/google/callback',
    ],


    'twitter' => [
        'client_id' => '',         // Your twitter Client ID
        'client_secret' => '', // Your twitter Client Secret
        'redirect' => 'http://fantastic.sc2hrpt7736.universe.wf/public/login/twitter/callback',
    ],

    'braintree' => [
        'model'  => App\Models\User::class,
        'environment' => env('BRAINTREE_ENV'),
        'merchant_id' => env('BRAINTREE_MERCHANT_ID'),
        'public_key' => env('BRAINTREE_PUBLIC_KEY'),
        'private_key' => env('BRAINTREE_PRIVATE_KEY'),
    ],

    'razorpay' => [
        'key' => 'rzp_test_yltsH9jI7Y5gFo',
        'secret' => '9km0kMtOjh00nIHjlzaDK6Hc'
    ],

    'fcm' => [
        'key' => '',
    ]

];