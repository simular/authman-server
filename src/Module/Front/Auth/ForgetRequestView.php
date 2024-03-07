<?php

declare(strict_types=1);

namespace App\Module\Front\Auth;

use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\ViewModel;
use Windwalker\Core\Language\TranslatorTrait;
use Windwalker\Core\View\View;
use Windwalker\Core\View\ViewModelInterface;

/**
 * The ForgetRequestView class.
 */
#[ViewModel(
    layout: [
        'default' => 'forget-request',
        'complete' => 'forget-request-complete',
    ],
    js: 'forget-request.js'
)]
class ForgetRequestView implements ViewModelInterface
{
    use TranslatorTrait;

    /**
     * Constructor.
     */
    public function __construct()
    {
        //
    }

    /**
     * Prepare View.
     *
     * @param  AppContext  $app   The web app context.
     * @param  View        $view  The view object.
     *
     * @return  mixed
     */
    public function prepare(AppContext $app, View $view): array
    {
        $view->setTitle($this->trans('luna.forget.request.title'));

        return [];
    }
}
