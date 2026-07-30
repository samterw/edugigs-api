<?php

return [

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

    'billplz' => [
        'key'           => env('BILLPLZ_KEY'),
        'url'           => env('BILLPLZ_URL'),
        'collection_id' => env('BILLPLZ_COLLECTION_ID'),
        'callback_url'  => env('BILLPLZ_CALLBACK_URL'),
        'redirect_url'  => env('BILLPLZ_REDIRECT_URL'),
    ],

];