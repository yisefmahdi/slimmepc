<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Firebase Cloud Messaging (admin push)
    |--------------------------------------------------------------------------
    | Push notifications for admin users, sent alongside every admin e-mail.
    | When no credentials are configured the AdminPushNotifier degrades
    | gracefully (logs a warning, never throws, mail flow is untouched).
    |
    | Setup: create a project at console.firebase.google.com, enable Cloud
    | Messaging, download the service-account JSON and point
    | FIREBASE_CREDENTIALS at it (absolute path, OUTSIDE public_html).
    */

    'enabled' => env('FIREBASE_ENABLED', true),

    'credentials' => env('FIREBASE_CREDENTIALS', ''),

    'project_id' => env('FIREBASE_PROJECT_ID', ''),

    // Public web config (used by the admin JS to obtain an FCM token).
    'web' => [
        'api_key' => env('FIREBASE_API_KEY', ''),
        'auth_domain' => env('FIREBASE_AUTH_DOMAIN', ''),
        'sender_id' => env('FIREBASE_SENDER_ID', ''),
        'app_id' => env('FIREBASE_APP_ID', ''),
        'vapid_key' => env('FIREBASE_VAPID_KEY', ''),
    ],
];
