<?php

namespace App\Routes;

use App\Module\Admin\Dashboard\DashboardController;
use App\Module\Admin\Dashboard\DashboardView;
use App\Module\Backup\BackupController;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('backup')
    ->register(function (RouteCreator $router) {
        $router->any('backup', '/backup')
            ->controller(BackupController::class, 'backup');
    });
