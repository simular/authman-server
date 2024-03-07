<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\Luna\Module\Admin\Auth\AuthController;
use Lyrasoft\Luna\Module\Admin\Auth\AuthLoginView;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('auth')
    ->register(function (RouteCreator $router) {
        $router->any('login', '/login')
            ->controller(AuthController::class)
            ->view(AuthLoginView::class);

        $router->any('logout', '/logout')
            ->controller(AuthController::class, 'logout');

        $router->any('auth_ajax', '/auth/ajax[/{task}]')
            ->controller(AuthController::class, 'ajax');
    });
