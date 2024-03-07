<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\Luna\Module\Admin\Widget\WidgetController;
use Lyrasoft\Luna\Module\Admin\Widget\WidgetEditView;
use Lyrasoft\Luna\Module\Admin\Widget\WidgetListView;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('widget')
    ->extra('menu', ['sidemenu' => 'widget_list'])
    ->register(function (RouteCreator $router) {
        $router->any('widget_list', '/widget/list')
            ->controller(WidgetController::class)
            ->view(WidgetListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('widget_edit', '/widget/edit[/{id}]')
            ->controller(WidgetController::class)
            ->view(WidgetEditView::class);
    });
