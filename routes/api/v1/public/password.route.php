<?php

declare(strict_types=1);

namespace App\Routes;

use App\Module\Api\AuthController;
use App\Module\Api\PasswordController;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('password')
    ->prefix('password')
    ->controller(PasswordController::class)
    ->register(function (RouteCreator $router) {
        $router->post('/challenge')
            ->handler('challenge');

        $router->post('/reset')
            ->handler('reset');
    });
