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

?>

@extends('global.html')

@section('superbody')
    <div class="account-pages my-5 pt-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card overflow-hidden">
                        <div class="card-body">
                            @section('header')
                                <div class="auth-logo text-center p-4">
                                    <img style="height: 45px" src="{{ $asset->path('images/logo-cb-h.svg') }}" alt="LOGO">

                                    <h4 class="mt-3 mb-0">後台管理</h4>
                                </div>
                            @show
                            <div class="p-2">

                                @section('message')
                                    @include('@messages')
                                @show

                                @yield('container', 'Container')
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@stop
