<?php

return [
    'auth' => [
        'terminal_id'  => env('URWAY_TERMINAL_ID'),
        'password'     => env('URWAY_PASSWORD'),
        'merchant_key' => env('URWAY_MERCHANT_KEY'),
    ],
    'base_url' => env('URWAY_BASE_URL', 'https://payments-dev.urway-tech.com/'),


    'card'   => 'CARD',
    'sadad'  => 'SADAD',
    'stc'    => 'STC_PAY',
    'mada'    => 'MADA',
    'applepay'    => 'APPLE',

];
