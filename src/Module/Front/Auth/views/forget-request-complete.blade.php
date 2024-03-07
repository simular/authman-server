<?php

declare(strict_types=1);

namespace App\View;

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

use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;

?>

@extends($app->config('luna.view_extends.front.auth') ?? 'global.auth')

@section('content')
    <div class="l-forget-request-complete container">
        <div class="text-center">
            <div class="mb-4">
                <span class="fa fa-inbox fa-4x"></span>
            </div>
            <p class="lead">
                @lang('luna.forget.request.complete.desc')
            </p>
        </div>
    </div>
@stop
