<?php

namespace App\Routes;

use App\Module\Admin\Dashboard\DashboardController;
use App\Module\Admin\Dashboard\DashboardView;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('dashboard')
    ->register(function (RouteCreator $router) {
        $router->any('home', '/')
            ->controller(DashboardController::class)
            ->view(DashboardView::class)
            ->redirect('article_list');
    });
