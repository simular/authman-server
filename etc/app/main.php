<?php

declare(strict_types=1);

return array_merge(
    require __DIR__ . '/windwalker.php',
    [
        'middlewares' => [
            \Windwalker\Core\Middleware\CorsMiddleware::class,
            \Windwalker\Core\Middleware\RoutingMiddleware::class,
        ],

        'listeners' => [
            //
        ],

        'http' => [
            'trusted_proxies' => env('PROXY_TRUSTED_IPS'),
            'trusted_headers' => [
                'x-forwarded-for',
                'x-forwarded-host',
                'x-forwarded-proto',
                'x-forwarded-port',
                'x-forwarded-prefix',
            ]
        ],
    ],
    require __DIR__ . '/../config.php'
);
