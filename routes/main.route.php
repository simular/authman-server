<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2020 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Routes;

use Windwalker\Core\Middleware\ForceSslMiddleware;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('web')
    ->middleware(
        ForceSslMiddleware::class,
        enabled: (bool) env('FORCE_SSL')
    )
    ->register(function (RouteCreator $router) {
        $router->load(__DIR__ . '/api.route.php');
        $router->load(__DIR__ . '/front.route.php');
        $router->load(__DIR__ . '/admin.route.php');

        $router->load(__DIR__ . '/packages/*.route.php');
    });
