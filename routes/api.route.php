<?php

declare(strict_types=1);

namespace App\Routes;

use Windwalker\Core\Middleware\JsonApiMiddleware;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('api')
    ->prefix('/api')
    ->namespace('api')
    ->register(function (RouteCreator $router) {
        $router->middleware(JsonApiMiddleware::class)
            ->register(
                function (RouteCreator $router) {
                    $router->load(__DIR__ . 'api/*.php');
                }
            );
    });
