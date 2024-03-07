<?php

namespace App\Routes;

use Lyrasoft\Contact\Module\Admin\Contact\ContactController;
use Lyrasoft\Contact\Module\Admin\Contact\ContactEditView;
use Lyrasoft\Contact\Module\Admin\Contact\ContactListView;
use Unicorn\Middleware\KeepUrlQueryMiddleware;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('contact')
    ->extra('menu', ['sidemenu' => 'contact_list'])
    ->middleware(
        KeepUrlQueryMiddleware::class,
        options: [
            'key' => 'type',
            'default' => 'main'
        ]
    )
    ->register(function (RouteCreator $router) {
        $router->any('contact_list', '/contact/list/{type}')
            ->controller(ContactController::class)
            ->view(ContactListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('contact_edit', '/contact/edit/{type}[/{id}]')
            ->controller(ContactController::class)
            ->view(ContactEditView::class);
    });
