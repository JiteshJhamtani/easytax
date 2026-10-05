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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'cross_server' => [
        'secret' => env('CROSS_SERVER_SECRET'),
    ],

    'easytax' => [
        'external_secret' => env('EASYTAX_EXTERNAL_SECRET', 'et_live_sec_89347519283741928347'),
        'drupal_webhook_url' => env(
            'DRUPAL_WEBHOOK_URL',
            str_contains(env('APP_URL', ''), 'easytax.live') || env('APP_ENV') === 'production'
                ? 'https://easytax.live/api/v1/filing/status-sync'
                : 'http://easytaxdesign.local/api/v1/filing/status-sync'
        ),
    ],

];
