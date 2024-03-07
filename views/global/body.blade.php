<?php

declare(strict_types=1);

namespace App\View;

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

use Lyrasoft\Luna\Services\ConfigService;
use Lyrasoft\Luna\User\UserService;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\Attributes\ViewModel;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;

$coreConfig = $app->service(ConfigService::class)->getConfig('core');

$menu = $app->service(\Lyrasoft\Luna\Services\MenuService::class)
    ->loadMenuFromFile('mainmenu', WINDWALKER_RESOURCES . '/menu/front/mainmenu.menu.php');

$user = $app->service(UserService::class)->getUser();

?>

@extends('global.html')

@if ($ga = trim((string) $coreConfig->get('ga')))
@push('meta')
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga }}"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', '{{ $ga }}');
    </script>
@endpush
@endif

@section('superbody')
    @section('header')

        <header>
            <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
                <div class="container">
                    <a class="navbar-brand" href="{{ $uri->path() }}">
                        <img src="{{ $asset->path('images/logo-cw-h.svg') }}"
                            alt="LOGO"
                            style="height: 27px;"
                        />
                    </a>
                    <button class="navbar-toggler" type="button"
                        data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <x-menu-root :menu="$menu" dropdown class="navbar-nav me-auto mb-2 mb-lg-0"></x-menu-root>

                        <ul class="navbar-nav mb-2 mb-lg-0">
                            <x-locale-dropdown class="nav-item" />

                            @if (!$user->isLogin())
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ $nav->to('login')->withReturn() }}">
                                        <span class="fa fa-sign-in-alt"></span>
                                        Login
                                    </a>
                                </li>
                            @else
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ $nav->to('logout') }}">
                                        <span class="fa fa-sign-out-alt"></span>
                                        Logout
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
    @show

    @section('body')
        @section('message')
            @include('@messages')
        @show

        @yield('content', 'Content')
    @show

    @section('copyright')
        <div id="copyright">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">

                        <hr />

                        <footer>
                            &copy; Windwalker {{ $chronos->localNow('Y') }}
                        </footer>
                    </div>
                </div>
            </div>
        </div>
    @show
@stop
