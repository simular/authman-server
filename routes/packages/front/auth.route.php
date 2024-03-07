<?php

/**
 * Part of earth project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Routes;

/** @var $router RouteCreator */

use Lyrasoft\Luna\Module\Front\Auth\AuthController;
use Lyrasoft\Luna\Module\Front\Auth\AuthLoginView;
use Lyrasoft\Luna\Module\Front\Auth\ForgetCompleteView;
use Lyrasoft\Luna\Module\Front\Auth\ForgetController;
use Lyrasoft\Luna\Module\Front\Auth\ForgetRequestView;
use Lyrasoft\Luna\Module\Front\Auth\ForgetResetView;
use Lyrasoft\Luna\Module\Front\Registration\RegistrationView;
use Windwalker\Core\Router\RouteCreator;

$router->group('auth')
    ->register(
        function (RouteCreator $router) {
            // Login
            $router->any('login', '/login')
                ->postHandler(AuthController::class, 'login')
                ->view(AuthLoginView::class);

            $router->any('logout', '/logout')
                ->controller(AuthController::class, 'logout');

            // Registration
            $router->any('registration', '/registration')
                ->postHandler(AuthController::class, 'register')
                ->view(RegistrationView::class);

            $router->get('registration_activate', '/registration/activate')
                ->controller(AuthController::class, 'activate');

            // Social Login
            $router->any('social_auth', '/social/auth/{provider}')
                ->controller(AuthController::class, 'socialAuth');

            // Activate
            $router->any('resend_activate', '/auth/resend/activate')
                ->controller(AuthController::class, 'resend');

            // Check Account
            $router->any('account_check', '/auth/account/check')
                ->controller(AuthController::class, 'accountCheck');

            // Forget Password
            $router->any('forget_request', '/forget/request[/{layout}]')
                ->postHandler(ForgetController::class, 'request')
                ->view(ForgetRequestView::class);

            $router->any('forget_confirm', '/forget/confirm')
                ->controller(ForgetController::class, 'confirm');

            $router->any('forget_reset', '/forget/reset[/{layout}]')
                ->postHandler(ForgetController::class, 'reset')
                ->view(ForgetResetView::class);

            $router->any('forget_complete', '/forget/complete')
                ->view(ForgetCompleteView::class);
        }
    );
