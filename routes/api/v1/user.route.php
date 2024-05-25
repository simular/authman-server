<?php

declare(strict_types=1);

namespace App\Routes;

use App\Module\Api\UserController;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('user')
    ->prefix('user')
    ->controller(UserController::class)
    ->register(
        function (RouteCreator $router) {
            $router->any('/me')
                ->getHandler('me')
                ->deleteHandler('deleteMe');

            $router->post('/sessions/refresh')
                ->handler('refreshSessions');
        }
    );
