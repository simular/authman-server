<?php

/**
 * Global variables
 * --------------------------------------------------------------
 * @var $app       AppContext      Application context.
 * @var $vm        object          The view model object.
 * @var $uri       SystemUri       System Uri information.
 * @var $chronos   ChronosService  The chronos datetime service.
 * @var $nav       Navigator       Navigator object to build route.
 * @var $asset     AssetService    The Asset manage service.
 * @var $lang      LangService     The language translation service.
 */

declare(strict_types=1);

use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;

?>

@extends('global.html')

@section('superbody')
<div id="layout-wrapper" uni-cloak>
    {{-- Header --}}
    @section('header')
        @include('admin.global.layout.header')
    @show

    {{-- Main Container --}}
    @section('container')
        {{-- Sidebar --}}
        @section('sidebar')
            <div class="vertical-menu">
                <div data-simplebar class="h-100">
                    <!--- Sidemenu -->
                    <div id="sidebar-menu">
                        <!-- Left Menu Start -->
                        <ul class="metismenu list-unstyled" id="side-menu">
                            @include('global.layout.sidemenu')
                        </ul>
                    </div>
                </div>
            </div>
        @show

    <div class="main-content" style="overflow: visible">

        <div class="page-content">
            <div class="container-fluid">
                @yield('body', 'Body Section')
            </div> <!-- container-fluid -->
        </div>
        <!-- End Page-content -->

        @section('copyright')
            @include('admin.global.layout.footer')
        @show
    @show
</div>
@stop
