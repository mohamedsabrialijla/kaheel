<?php

return [
    'gateway' => 'neoleap',
    'mode'    => env('NEOLEAP_MODE', 'live'), // live | test
    'status'  => (int) env('NEOLEAP_STATUS', 1),
    // السيرفر محجوب من Neoleap مباشرة — نفس proxy يقين (عطّله: NEOLEAP_PROXY=none)
    'proxy'           => env('NEOLEAP_PROXY', 'http://8.213.38.124:3128'),
    'connect_timeout' => (int) env('NEOLEAP_CONNECT_TIMEOUT', 15),
    'timeout'         => (int) env('NEOLEAP_TIMEOUT', 60),

    'live' => [
        'tranportal_id'       => env('NEOLEAP_TRANPORTAL_ID', 'mDMy7yR296Ump8O'),
        'tranportal_password' => env('NEOLEAP_TRANPORTAL_PASSWORD', 'sV#Z$QL0f4@cc98'),
        'resource_key'        => env('NEOLEAP_RESOURCE_KEY', '53419115046053419115046053419115'),
    ],

    'test' => [
        'tranportal_id'       => env('NEOLEAP_TEST_TRANPORTAL_ID', 'mDMy7yR296Ump8O'),
        'tranportal_password' => env('NEOLEAP_TEST_TRANPORTAL_PASSWORD', 'sV#Z$QL0f4@cc98'),
        'resource_key'        => env('NEOLEAP_TEST_RESOURCE_KEY', '53419115046053419115046053419115'),
    ],
];
