<?php

namespace App\Routes;

use Lyrasoft\Contact\Module\Front\Contact\ContactController;
use Lyrasoft\Contact\Module\Front\Contact\ContactView;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('contact')
    ->register(function (RouteCreator $router) {
        $router->any('contact', '/contact')
            ->controller(ContactController::class)
            ->view(ContactView::class);
    });
