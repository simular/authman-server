<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\Luna\Module\Admin\Category\CategoryController;
use Lyrasoft\Luna\Module\Admin\Category\CategoryEditView;
use Lyrasoft\Luna\Module\Admin\Category\CategoryListView;
use Unicorn\Middleware\KeepUrlQueryMiddleware;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('category')
    ->extra('menu', ['sidemenu' => 'category_list'])
    ->middleware(KeepUrlQueryMiddleware::di(options: ['key' => 'type',]))
    ->register(
        function (RouteCreator $router) {
            // Category
            $router->any('category_edit', '/category/edit/{type}[/{id}]')
                ->controller(CategoryController::class)
                ->view(CategoryEditView::class)
                ->extra('layout', 'country');

            // Categories
            $router->any('category_list', '/category/list/{type}')
                ->controller(CategoryController::class)
                ->postHandler('copy')
                ->patchHandler('batch')
                ->putHandler('filter')
                ->deleteHandler('delete')
                ->view(CategoryListView::class);

            // Ajax Category List
            $router->any('category_ajax_list', '/category/ajax/list/{type}')
                ->controller(CategoryController::class, 'ajaxList');

            // Ajax Category List
            $router->any('category_tree', '/category/ajax/tree/{type}')
                ->controller(CategoryController::class, 'tree');
        }
    );
