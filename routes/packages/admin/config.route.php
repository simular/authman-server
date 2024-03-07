<?php

/**
 * Part of phoenix project.
 *
 * @copyright  Copyright {C} 2016 {ORGANIZATION}. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

namespace App\Routes;

use Lyrasoft\Luna\Module\Admin\Config\ConfigController;
use Lyrasoft\Luna\Module\Admin\Config\ConfigEditView;
use Lyrasoft\Luna\Module\Admin\Config\Form\CoreForm;
use Unicorn\Middleware\KeepUrlQueryMiddleware;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->group('config')
    ->register(
        function (RouteCreator $router) {
            $router->any('config_core', '/config/core')
                ->controller(ConfigController::class)
                ->view(ConfigEditView::class)
                ->var('type', 'core')
                ->var('form', CoreForm::class);
        }
    );
