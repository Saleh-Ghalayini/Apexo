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
        'client_id' => env('SLACK_CLIENT_ID'),
        'client_secret' => env('SLACK_CLIENT_SECRET'),
        'redirect' => env('SLACK_REDIRECT_URI'),
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'project_id' => env('GOOGLE_PROJECT_ID'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'notion' => [
        'client_id' => env('NOTION_CLIENT_ID'),
        'client_secret' => env('NOTION_CLIENT_SECRET'),
        'redirect' => env('NOTION_REDIRECT_URI'),
    ],

    'openai' => [
        'secret' => env('OPENAI_API_KEY'),
        'url' => env('OPENAI_URL', 'https://api.openai.com/v1'),
        'model' => env('OPENAI_DEFAULT_MODEL', 'gpt-4o'),
        'temperature' => env('OPENAI_TEMPERATURE', 0.7),
    ],

    'google_calendar' => [
        'credentials_path' => env('GOOGLE_CALENDAR_CREDENTIALS_PATH'),
    ],
];
