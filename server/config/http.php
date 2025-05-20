<?php

return [
    /*
    |--------------------------------------------------------------------------
    | HTTP Client Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the HTTP client options used by the application,
    | especially for external API calls like OpenAI.
    |
    */

    'timeout' => env('HTTP_TIMEOUT', 30), // Default timeout of 30 seconds

    'connect_timeout' => env('HTTP_CONNECT_TIMEOUT', 15), // Default connect timeout of 15 seconds

    'retry' => [
        'max_attempts' => env('HTTP_RETRY_MAX_ATTEMPTS', 3),
        'initial_delay' => env('HTTP_RETRY_INITIAL_DELAY', 1000), // milliseconds
        'max_delay' => env('HTTP_RETRY_MAX_DELAY', 5000), // milliseconds
    ],
];
