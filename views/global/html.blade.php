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

use Unicorn\Script\UnicornScript;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\Attributes\ViewModel;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Html\HtmlFrame;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;

$app->service(UnicornScript::class)->disableTransitionBeforeLoad();

$htmlFrame = $app->service(HtmlFrame::class);
$htmlFrame->getHtmlElement()
    ->setAttribute('lang', $app->config('language.locale') ?: $app->config('language.fallback', 'en-US'));

$htmlFrame->addBodyClass('env-' . ($app->getMode() ?: 'prod'));

?><!DOCTYPE html>
<html {!! $htmlFrame->htmlAttributes() !!}>
<head>
    <base href="{{ $uri::normalize($uri->path) ?: '/' }}" />

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <title>{{ $htmlFrame->getPageTitle() }}</title>

    <link rel="shortcut icon" type="image/x-icon"
        href="{{ $htmlFrame->getFavicon() ?? $asset->path('images/favicon.png') }}" />
    {!! $htmlFrame->renderMetadata() !!}
@stack('meta')
@yield('meta')

    {!! $asset->renderCSS(true) !!}
@stack('style')

    {!! $htmlFrame->renderCustomTags() !!}
@stack('head')
</head>
<body {!! $htmlFrame->bodyAttributes() !!}>

@yield('superbody')

{!! $asset->renderCSS(true, [], true) !!}
@stack('footerStyle')

{{-- Bottom Scripts --}}
{!! $asset->getTeleport()->render() !!}
{!! $asset->getImportMap()->render() !!}
{!! $asset->getImportMap()->render('systemjs-importmap') !!}
{!! $asset->renderJS(true) !!}
@stack('script')

</body>
</html>
