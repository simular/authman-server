<?php

namespace Lyrasoft\Portfolio\Routes;

use Lyrasoft\Portfolio\Module\Admin\Portfolio\PortfolioController;
use Lyrasoft\Portfolio\Module\Admin\Portfolio\PortfolioEditView;
use Lyrasoft\Portfolio\Module\Admin\Portfolio\PortfolioListView;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('portfolio')
    ->extra('menu', ['sidemenu' => 'portfolio_list'])
    ->register(function (RouteCreator $router) {
        $router->any('portfolio_list', '/portfolio/list')
            ->controller(PortfolioController::class)
            ->view(PortfolioListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('portfolio_edit', '/portfolio/edit[/{id}]')
            ->controller(PortfolioController::class)
            ->view(PortfolioEditView::class);
    });
