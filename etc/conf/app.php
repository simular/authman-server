<?php

declare(strict_types=1);

use Windwalker\Core\Provider\DateTimeProvider;

return [
    'secret' => env('APP_SECRET'),

    'name' => 'Authman',

    'debug' => (bool) (env('APP_DEBUG') ?? false),

    'mode' => env('APP_ENV', 'prod'),

    'timezone' => env('APP_TIMEZONE', 'Asia/Taipei'),

    'server_timezone' => env('APP_SERVER_TIMEZONE', 'UTC'),

    'dump_server' => [
        'host' => env('DUMP_SERVER_HOST') ?: 'tcp://127.0.0.1:9912'
    ],

    'providers' => [
        DateTimeProvider::class
    ]
];
