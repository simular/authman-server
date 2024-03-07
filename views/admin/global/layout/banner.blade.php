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

$htmlFrame = $app->service(\Windwalker\Core\Html\HtmlFrame::class);

?>

<!-- start page title -->
<div class="page-title-box d-flex align-items-center justify-content-between position-sticky py-2 mb-3"
    style="background-color: var(--bs-body-bg); top: 70px; z-index: 4; margin-top: -1rem">
    <h4 class="mb-sm-0 font-size-18">
        {{ $htmlFrame->getTitle() }}
    </h4>

    <div class="d-inline-block d-lg-none">
        <button class="btn btn-sm btn-primary"
            type="button"
            data-bs-toggle="offcanvas"
            data-bs-target="#toolbar-offcanvas"
            aria-controls="toolbar-offcanvas">
            <i class="fa fa-bars"></i>
            Toolbar
        </button>
    </div>

    <div id="toolbar-offcanvas" class="offcanvas-lg offcanvas-end"
        style="top: 70px">
        <div class="offcanvas-body">
            @section('admin-toolbar')
                @include('admin.global.layout.toolbar')
            @show
        </div>
    </div>
</div>
<!-- end page title -->
