<?php

/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Routes;

use Lyrasoft\Luna\Module\Core\CaptchaController;
use Lyrasoft\Luna\Module\Core\LocaleController;
use Windwalker\Core\Router\RouteCreator;

/** @var RouteCreator $router */

$router->any('_captcha_image', '/_captcha/image')
    ->controller(CaptchaController::class, 'image');

$router->any('locale_switch', '/locale/switch/{alias}')
    ->controller(LocaleController::class, 'switch');
