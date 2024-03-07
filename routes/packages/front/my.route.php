<?php

/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\Luna\Module\Front\Profile\ProfileController;
use Lyrasoft\Luna\Module\Front\Profile\ProfileEditView;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('my')
    ->prefix('/my')
    ->register(function (RouteCreator $router) {
        $router->any('profile_edit', '/profile/edit')
            ->controller(ProfileController::class)
            ->view(ProfileEditView::class);
    });
