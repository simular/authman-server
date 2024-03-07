<?php

namespace Lyrasoft\Member\Routes;

use Lyrasoft\Member\Module\Admin\Member\MemberController;
use Lyrasoft\Member\Module\Admin\Member\MemberEditView;
use Lyrasoft\Member\Module\Admin\Member\MemberListView;
use Windwalker\Core\Router\RouteCreator;

/** @var  RouteCreator $router */

$router->group('member')
    ->extra('menu', ['sidemenu' => 'member_list'])
    ->register(function (RouteCreator $router) {
        $router->any('member_list', '/member/list')
            ->controller(MemberController::class)
            ->view(MemberListView::class)
            ->postHandler('copy')
            ->putHandler('filter')
            ->patchHandler('batch');

        $router->any('member_edit', '/member/edit[/{id}]')
            ->controller(MemberController::class)
            ->view(MemberEditView::class);
    });
