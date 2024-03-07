<?php

declare(strict_types=1);

namespace App\Module\Front\Auth;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Lyrasoft\Luna\Auth\SRP\SRPService;
use App\Entity\User;
use Lyrasoft\Luna\LunaPackage;
use Lyrasoft\Luna\User\UserService;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\ViewModel;
use Windwalker\Core\Language\TranslatorTrait;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\View\View;
use Windwalker\Core\View\ViewModelInterface;
use Windwalker\Data\Collection;
use Windwalker\ORM\ORM;

/**
 * The ForgetResetView class.
 */
#[ViewModel(
    layout: [
        'default' => 'forget-reset',
        'complete' => 'forget-reset-complete',
    ],
    js: 'forget-reset.js'
)]
class ForgetResetView implements ViewModelInterface
{
    use TranslatorTrait;

    /**
     * Constructor.
     */
    public function __construct(protected Navigator $nav, protected SRPService $srp)
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
    public function prepare(AppContext $app, View $view): mixed
    {
        $token = $app->getState()->get('reset.token');

        if (!$token) {
            return $this->nav->to('home');
        }

        $view->setTitle($this->trans('luna.reset.form.title'));

        $identity = '';

        if ($this->srp->isEnabled()) {
            $payload = JWT::decode(
                $token,
                new Key($app->getSecret(), 'HS256'),
            );

            $email = $payload->email ?? null;

            /** @var User $user */
            $user = $app->retrieve(ORM::class)
                ->findOne(User::class, ['email' => (string) $email], Collection::class);

            $loginName = $app->service(LunaPackage::class)->getLoginName();

            $identity = $user->$loginName;
        }

        return compact('token', 'identity');
    }
}
