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

$menu = $app->service(\Unicorn\Legacy\Html\MenuHelper::class);
?>

<li class="menu-title" key="t-menu">Menu</li>

<li class="">
    <a href="{{ $nav->to('user_list') }}"
        class="{{ $menu->active('user_list') }}">
        <i class="fa-light fa-fw fa-users"></i>
        <span>Users</span>
    </a>
</li>

<li class="">
    <a href="javascript: void(0);" class="has-arrow waves-effect" aria-expanded="false">
        <i class="fa fa-fw fa-newspaper"></i>
        <span>CMS</span>
    </a>
    <ul class="sub-menu mm-collapse" aria-expanded="false" style="">
        <li>
            <a href="{{ $nav->to('article_list') }}"
                class="{{ $menu->active('article_list') }}">
                <span class="fa fa-fw fa-newspaper small"></span>
                <span>Articles</span>
            </a>
        </li>
    </ul>
</li>

<li class="">
    <a href="{{ $nav->to('config_core') }}"
        class="{{ $menu->active('config_core') }}">
        <i class="fa fa-fw fa-cog"></i>
        <span>@lang('luna.config.title', $lang('luna.config.type.core'))</span>
    </a>
</li>

{{-- @muse-placeholder  submenu  Do not remove this line --}}
