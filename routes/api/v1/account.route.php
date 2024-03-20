<?php

declare(strict_types=1);

namespace App\Routes;

use App\Module\Api\AccountController;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('account')
    ->prefix('account')
    ->controller(AccountController::class)
    ->register(function (RouteCreator $router) {
        $router->get('/list')
            ->handler('items');

        $router->get('/logo/search')
            ->handler('logoSearch');
    });
