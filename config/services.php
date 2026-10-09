<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],


    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],


    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'sms' => [
        'url' => env('SMS_API_URL'),
        'key' => env('SMS_API_KEY'),
        'sender' => env('SMS_SENDER', 'Kaheel'),
    ],

    'bricks' => [
        'url' => env('BRICKS_API_URL', 'https://api.brickspms.com'),
        'slug' => env('BRICKS_SLUG', 'demo'),
    ],



    'firebase' => [
        'project_id' => env('FIREBASE_PROJECT_ID'),
        'credentials' => storage_path('app/firebase_credentials.json'),
        'server_key' => env('FIREBASE_SERVER_KEY'),
        'log_payloads' => env('FCM_LOG_PAYLOADS', false),
        'action_base_url' => env('FCM_ACTION_BASE_URL', 'https://www.brandy.sa'),

    ],

    /*
    |--------------------------------------------------------------------------
    | Nafath (نفاذ) via Semati/ELM API
    | الرابط لا يُفتح في المتصفح — يُستدعى من السيرفر فقط (POST + API Key وربما App ID).
    | - authorize_url: الرابط الكامل (مثال: https://rabet-nafath.api.elm.sa/nafath/api/v1/client/authorize/)
    | - app_id: إن طلبته وثيقة ELM/نفاذ أضفه هنا
    */
    'nafath' => [
        'authorize_url' => env('NAFATH_AUTHORIZE_URL', ''),
        'url_base' => env('NAFATH_URL_BASE', 'test'),
        'app_id' => env('NAFATH_APP_ID', ''),
        'api_key' => env('NAFATH_API_KEY', ''),
        'callback_url' => env('NAFATH_CALLBACK_URL', ''),
    ],

    'yakeen' => [
        'username' => env('YAKEEN_USERNAME', ''),
        'password' => env('YAKEEN_PASSWORD', ''),
        'app_id'   => env('YAKEEN_APP_ID', ''),
        'app_key'  => env('YAKEEN_APP_KEY', ''),
        'connect_timeout' => env('YAKEEN_CONNECT_TIMEOUT', 5),
        'timeout' => env('YAKEEN_TIMEOUT', 30),
        'retry_times' => env('YAKEEN_RETRY_TIMES', 2),
        'retry_sleep_ms' => env('YAKEEN_RETRY_SLEEP_MS', 300),
        // من دليل يقين (Appendix): 1000011021 — 100001010 مرفوض في TRIAL عندكم
        'operator_id' => env('YAKEEN_OPERATOR_ID', '1000011021'),
        'usage_code'  => env('YAKEEN_USAGE_CODE', 'USC10004'),
        /**
         * consent/create — dateOfBirth (حسب رد ELM: yyyy-mm)
         * - hijri_yyyy_mm     → 1379-07 (سعودي — هجري)
         * - gregorian_yyyy_mm → 1984-08 (إقامة — ميلادي)
         */
        'crosscheck_mode_nin'   => env('YAKEEN_CROSSCHECK_MODE_NIN', 'hijri_yyyy_mm'),
        'crosscheck_mode_iqama' => env('YAKEEN_CROSSCHECK_MODE_IQAMA', 'gregorian_yyyy_mm'),
    ],

];
