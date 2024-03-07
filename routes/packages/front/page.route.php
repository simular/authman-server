<?php

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\Luna\Module\Front\Page\PageView;
use Windwalker\Core\Router\RouteCreator;

/** @var $router RouteCreator */

$router->group('page')
    ->register(
        function (RouteCreator $router) {
            $router->any('page', '/page/{path:.+}')
                ->view(PageView::class);
        }
    );
