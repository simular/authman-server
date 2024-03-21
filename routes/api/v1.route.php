<?php

declare(strict_types=1);

namespace App\Routes;

use App\Middleware\ApiAuthMiddleware;
use Windwalker\Core\Middleware\JsonApiMiddleware;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('v1')
    ->prefix('v1')
    ->middleware(JsonApiMiddleware::class)
    ->register(
        function (RouteCreator $router) {
            $router->group('private')
                ->middleware(ApiAuthMiddleware::class)
                ->register(
                    function (RouteCreator $router) {
                        $router->load(__DIR__ . '/v1/*.php');
                    }
                );

            $router->group('public')
                ->load(__DIR__ . '/v1/public/*.php');
        }
    );
