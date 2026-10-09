<?php


return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Credentials
    |--------------------------------------------------------------------------
    |
    | Path to your Firebase service account JSON file
    | يمكنك تحميله من Firebase Console → Project Settings → Service Accounts
    |
    */
    'credentials' => env('FIREBASE_CREDENTIALS', storage_path('app/firebase/firebase-credentials.json')),

    /*
    |--------------------------------------------------------------------------
    | Firebase Database URL
    |--------------------------------------------------------------------------
    |
    | Your Firebase Realtime Database URL
    | مثال: https://your-project-id-default-rtdb.firebaseio.com
    |
    */
    'database_url' => env('FIREBASE_DATABASE_URL', 'https://your-project-default-rtdb.firebaseio.com'),

    /*
    |--------------------------------------------------------------------------
    | Firebase Storage Bucket
    |--------------------------------------------------------------------------
    |
    | Your Firebase Storage bucket name
    | مثال: your-project-id.appspot.com
    |
    */
    'storage_bucket' => env('FIREBASE_STORAGE_BUCKET', 'your-project.appspot.com'),

    /*
    |--------------------------------------------------------------------------
    | Firebase API Key
    |--------------------------------------------------------------------------
    |
    | Your Firebase Web API Key (for frontend)
    |
    */
    'api_key' => env('FIREBASE_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Firebase Auth Domain
    |--------------------------------------------------------------------------
    |
    | Your Firebase Auth Domain
    |
    */
    'auth_domain' => env('FIREBASE_AUTH_DOMAIN', 'your-project.firebaseapp.com'),

    /*
    |--------------------------------------------------------------------------
    | Firebase Project ID
    |--------------------------------------------------------------------------
    |
    | Your Firebase Project ID
    |
    */
    'project_id' => env('FIREBASE_PROJECT_ID', 'your-project-id'),

    /*
    |--------------------------------------------------------------------------
    | Firebase Messaging Sender ID
    |--------------------------------------------------------------------------
    |
    | Your Firebase Messaging Sender ID
    |
    */
    'messaging_sender_id' => env('FIREBASE_MESSAGING_SENDER_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Firebase App ID
    |--------------------------------------------------------------------------
    |
    | Your Firebase App ID
    |
    */
    'app_id' => env('FIREBASE_APP_ID', ''),
];
