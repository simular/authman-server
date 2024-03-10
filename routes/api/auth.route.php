<?php

declare(strict_types=1);

namespace App\Routes;

use App\Module\Api\Auth\AuthController;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('auth')
    ->prefix('auth')
    ->controller(AuthController::class)
    ->register(function (RouteCreator $router) {
        $router->any('/authenticate')
            ->handler('authenticate');

        $router->any('/challenge')
            ->handler('challenge');

        $router->post('/register')
            ->handler('register');
    });
