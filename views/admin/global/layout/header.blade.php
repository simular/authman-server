<?php

/**
 * Global variables
 * --------------------------------------------------------------
 * @var $app       AppContext      Application context.
 * @var $view      ViewModel       The view modal object.
 * @var $uri       SystemUri       System Uri information.
 * @var $chronos   ChronosService  The chronos datetime service.
 * @var $nav       Navigator       Navigator object to build route.
 * @var $asset     AssetService    The Asset manage service.
 * @var $lang      LangService     The language translation service.
 */

declare(strict_types=1);

use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\Attributes\ViewModel;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;

$user = $app->service(\Lyrasoft\Luna\User\UserService::class)->getUser();
?>

@section('header')
    <header id="page-topbar">
        <div class="navbar-header">
            <div class="d-flex">
                <!-- LOGO -->
                <div class="navbar-brand-box text-start">
                    {{--<a href="{{ $nav->to('home') }}" class="logo logo-dark">--}}
                    {{--    <span class="logo-sm">--}}
                    {{--        <img class="img-fluid" src="{{ $asset->path('images/logo-cw-h.svg') }}" alt="" height="22">--}}
                    {{--    </span>--}}
                    {{--    <span class="logo-lg">--}}
                    {{--        <img class="img-fluid" src="{{ $asset->path('images/logo-cw-h.svg') }}" alt="" height="17">--}}
                    {{--    </span>--}}
                    {{--</a>--}}

                    <a href="{{ $nav->to('home') }}" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="{{ $asset->path('images/icon.svg') }}" alt="" height="45"
                                style="margin-left: -12px">
                        </span>
                        <span class="logo-lg">
                            <img  src="{{ $asset->path('images/logo-cw-h.svg') }}" alt="" height="35">
                        </span>
                    </a>
                </div>

                <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect"
                    id="vertical-menu-btn">
                    <i class="fa fa-fw fa-bars"></i>
                </button>

                @section('nav')
                    @include('admin.global.layout.mainmenu')
                @show
            </div>

            <div class="d-flex">
                <div class="ms-1" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Fullscreen">
                    <button type="button" class="btn header-item noti-icon waves-effect d-flex align-items-center" data-bs-toggle="fullscreen">
                        <i class="fa-regular fa-expand"></i>
                    </button>
                </div>

                <div class="ms-1" data-bs-toggle="tooltip" data-bs-placement="bottom" title="See Frontend">
                    <a class="btn header-item noti-icon waves-effect d-flex align-items-center"
                        href="{{ $nav->to('front::home') }}"
                        target="_blank"
                    >
                        <i class="fa-regular fa-eye"></i>
                    </a>
                </div>

                @if ($user->isLogin())
                    <div class="dropdown d-inline-block">
                        <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="rounded-circle header-profile-user" src="{{ $user->getAvatar() }}"
                                alt="Header Avatar">
                            <span class="d-none d-xl-inline-block ms-1">{{ $user->getName() }}</span>
                            <i class="fa-regular fa-angle-down d-none d-xl-inline-block"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- item-->
                            <a class="dropdown-item" href="{{ $nav->to('user_edit')->id($user->getId()) }}">
                                <i class="fa-regular fa-user font-size-16 align-middle me-1"></i>
                                <span>My Profile</span>
                            </a>
                            <a class="dropdown-item text-danger" href="javascript://"
                                onclick="u.form().post('{{ $nav->to('logout') }}')">
                                <i class="fa-regular fa-power-off font-size-16 align-middle me-1 text-danger"></i>
                                <span key="t-logout">Logout</span></a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </header>
@show
