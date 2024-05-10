<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Routes;

use App\Module\Front\FrontMiddleware;
use App\Module\Front\Home\HomeController;
use App\Module\Front\Home\HomeView;
use Lyrasoft\Luna\Middleware\LocaleMiddleware;
use Windwalker\Core\Middleware\CsrfMiddleware;
use Windwalker\Core\Middleware\MaintenanceMiddleware;
use Windwalker\Core\Middleware\SefMiddleware;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('front')
    ->namespace('front')
    ->middleware(MaintenanceMiddleware::class)
    ->middleware(
        CsrfMiddleware::class,
        options: [
            'excludes' => [
                'front::backup',
            ]
        ]
    )
    ->middleware(LocaleMiddleware::class)
    ->middleware(FrontMiddleware::class)
    ->middleware(
        SefMiddleware::class,
        flags: SefMiddleware::REPLACE_LINK_HASH
    )
    ->register(function (RouteCreator $router) {
        $router->get('home', '/')
            ->redirect(fn (Navigator $nav) => $nav->createRouteUri('api/v1'));

        $router->load(__DIR__ . '/front/*.php');

        $router->load(__DIR__ . '/packages/front/*.route.php');

        $router->load(__DIR__ . '/custom/front/*.route.php');
    });
