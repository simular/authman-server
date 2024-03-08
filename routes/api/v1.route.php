<?php

declare(strict_types=1);

namespace App\Routes;

use App\Module\Api\Auth\AuthController;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('v1')
    ->prefix('v1')
    ->controller(AuthController::class)
    ->register(function (RouteCreator $router) {
        $router->any('v1_auth', '/auth/authenticate')
            ->handler('authenticate');
    });
