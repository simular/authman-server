<?php

declare(strict_types=1);

namespace App\Routes;

use App\Middleware\ApiAuthMiddleware;
use App\Module\Api\AuthController;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('auth')
    ->prefix('auth')
    ->controller(AuthController::class)
    ->register(function (RouteCreator $router) {
        $router->post('/authenticate')
            ->handler('authenticate');

        $router->post('/challenge')
            ->handler('challenge');

        $router->post('/register')
            ->handler('register');

        $router->any('/refreshToken')
            ->handler('refreshToken');

        $router->any('/me')
            ->handler('me')
            ->middleware(ApiAuthMiddleware::class);

        $router->any('/delete/me')
            ->handler('deleteMe')
            ->middleware(ApiAuthMiddleware::class);
    });
