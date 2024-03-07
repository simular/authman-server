<?php

declare(strict_types=1);

namespace App\Routes;

use App\Module\Api\Auth\AuthController;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('v1')
    ->prefix('v1')
    ->register(function (RouteCreator $router) {
        $router->any('v1_auth', '/auth/{task}')
            ->controller(AuthController::class, 'index');
    });
