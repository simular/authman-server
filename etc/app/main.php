<?php

declare(strict_types=1);

use Windwalker\Core\Http\CorsHandler;

return array_merge(
    require __DIR__ . '/windwalker.php',
    [
        'middlewares' => [
            \App\Middleware\ApiMiddleware::class,
            \Windwalker\DI\create(
                \Windwalker\Core\Middleware\CorsMiddleware::class,
                options: [
                    'send_instantly' => true,
                    'configure' => function (CorsHandler $cors) {
                        return $cors->allowHeaders(
                            [
                                'Authorization',
                                'Content-Type'
                            ]
                        )
                            ->allowMethods('*');
                    }
                ]
            ),
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
