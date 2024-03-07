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

use Lyrasoft\Luna\Auth\SRP\SRPService;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Asset\AssetService;
use Windwalker\Core\DateTime\ChronosService;
use Windwalker\Core\Language\LangService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\SystemUri;

$srp = $app->retrieve(SRPService::class);
?>

@extends($app->config('luna.view_extends.front.auth') ?? 'global.auth')

@section('content')
    <form id="reset-form" class="form-horizontal l-forget-request"
        action="{{ $nav->to('forget_reset') }}"
        uni-form-validate
        method="POST"
        enctype="multipart/form-data"
        {!! $srp->registerDirective() !!}
    >
        <div class="container">
            <div class="form-group mb-3" uni-field-validate>
                <label for="input-password" class="form-label">
                    @lang('luna.user.field.password')
                </label>
                <input id="input-password" type="password" name="password" class="form-control"
                    autocomplete="new-password"
                    required
                    data-role="password"
                    data-input-password
                />
                <div class="invalid-tooltip" data-field-error></div>
            </div>


            <div class="form-group mb-3" uni-field-validate>
                <label for="input-password2" class="form-label">
                    @lang('luna.user.field.password.confirm')
                </label>
                <input id="input-password2" type="password" name="password2" class="form-control"
                    autocomplete="new-password"
                    required
                    data-validate="password-confirm"
                    data-confirm-target="[data-role=password]"
                    data-custom-error-message="@lang('luna.message.password.not.match')"
                />
                <div class="invalid-tooltip" data-field-error></div>
            </div>

            <p class="reset-button-group">
                <button class="reset-button btn btn-primary btn-block"
                    data-dos>
                    @lang('luna.forget.button.reset')
                </button>
            </p>

            <div class="hidden-inputs">
                <input name="token" type="hidden" value="{{ $token ?? '' }}" />
                @if ($srp->isEnabled())
                    <input name="identity" type="hidden" value="{{ $identity ?? '' }}" data-input-identity />
                @endif
                @csrf
            </div>
        </div>
    </form>
@stop
