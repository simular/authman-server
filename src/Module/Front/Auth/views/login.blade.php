<?php

declare(strict_types=1);

namespace App\View;

/**
 * Global variables
 * --------------------------------------------------------------
 * @var  $app       AppContext      Application context.
 * @var  $vm        \App\Module\Front\Auth\AuthLoginView          The view model object.
 * @var  $uri       SystemUri       System Uri information.
 * @var  $chronos   ChronosService  The chronos datetime service.
 * @var  $nav       Navigator       Navigator object to build route.
 * @var  $asset     AssetService    The Asset manage service.
 * @var  $lang      LangService     The language translation service.
 */

use Lyrasoft\Luna\Auth\SRP\SRPService;
use Unicorn\Script\UnicornScript;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;

$srp = $app->service(SRPService::class);

$uniScript = $app->service(UnicornScript::class);
$uniScript->addRoute('@auth_ajax');

?>

@extends($app->config('luna.view_extends.front.auth') ?? 'global.auth')

@section('content')
    <div class="l-login container">
        <form id="login-form-extra" action="" method="post">
            @if ($reActivate ?? null)
                <div class="mb-4">
                    <div class="alert alert-info text-center">
                        <p>
                            @lang('luna.login.message.not.activated')
                        </p>
                        <div>
                            <button type="button" class="btn btn-info"
                                data-dos
                                onclick="form.action = '{{ $nav->to('resend_activate', ['email' => $reActivate]) }}'; form.requestSubmit()">
                                @lang('luna.button.resend.activate.mail')
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            @if ($vm->hasSocialProviders())
                <div class="d-flex flex-column">
                    @foreach ($vm->getSocialProviders() as $provider => $config)
                        <button class="btn btn-secondary mb-2"
                            type="button"
                            data-dos
                            onclick="form.action = '{{ $nav->to('social_auth')->var('provider', $provider) }}'; form.requestSubmit()">
                            <i class="fa-brands fa-{{ strtolower($provider) }}"></i>
                            {{ $provider }}
                        </button>
                    @endforeach

                    <div class="my-3 text-center">
                        OR
                    </div>
                </div>
            @endif

            <div class="d-none">
                <x-csrf></x-csrf>
            </div>
        </form>

        <form id="login-form" class="" action="{{ $nav->to('login') }}"
            uni-form-validate
            method="POST"
            enctype="multipart/form-data"
            {!! $srp->loginDirective() !!}
        >

            <x-fieldset :form="$form"></x-fieldset>

            <div class="d-sm-flex justify-content-between mb-5">
                {{--                <div id="input-user-remember-control mb-3 mb-sm-0" class="checkbox-field">--}}
                {{--                    <div class="form-check checkbox checkbox-primary">--}}
                {{--                        <input name="user[remember]" class="form-check-input" type="checkbox"--}}
                {{--                            id="input-user-remember" value="on">--}}
                {{--                        <label class="form-check-label" for="input-user-remember">--}}
                {{--                            @lang('luna.user.field.remember')--}}
                {{--                        </label>--}}
                {{--                    </div>--}}
                {{--                </div>--}}

                <div class="l-login__action ms-auto">
                    <a class="forget-link"
                        href="{{ $nav->to('forget_request') }}">
                        @lang('luna.button.forget')
                    </a>
                </div>
            </div>

            <div class="l-login__buttons c-login-buttons d-flex flex-column mb-4">
                <button type="submit"
                    class="c-login-button btn btn-primary mb-3"
                    data-dos
                >
                    @lang('luna.button.login')
                </button>
                <a class="c-register-button btn btn-success"
                    href="{{ $nav->to('registration') }}">
                    <span class="fa fa-user-plus"></span>
                    @lang('luna.button.register')
                </a>
            </div>

            <div class="hidden-inputs">
                @csrf
            </div>
        </form>
    </div>
@stop
