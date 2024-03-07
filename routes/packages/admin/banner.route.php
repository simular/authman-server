<?php

namespace App\Routes;

use Lyrasoft\Banner\Module\Admin\Banner\BannerController;
use Lyrasoft\Banner\Module\Admin\Banner\BannerEditView;
use Lyrasoft\Banner\Module\Admin\Banner\BannerListView;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('banner')
    ->extra('menu', ['sidemenu' => 'banner_list'])
    ->register(function (RouteCreator $router) {
        $router->any('banner_list', '/banner/list')
            ->controller(BannerController::class)
            ->view(BannerListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('banner_edit', '/banner/edit[/{id}]')
            ->controller(BannerController::class)
            ->view(BannerEditView::class);
    });
