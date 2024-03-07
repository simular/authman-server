<?php

declare(strict_types=1);

namespace App\Module\Front\Auth;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Lyrasoft\Luna\Auth\SRP\SRPService;
use App\Entity\User;
use Lyrasoft\Luna\User\UserService;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\Controller;
use Windwalker\Core\Form\Exception\ValidateFailException;
use Windwalker\Core\Language\TranslatorTrait;
use Windwalker\Core\Mailer\MailerInterface;
use Windwalker\Core\Renderer\RendererService;
use Windwalker\Core\Router\Navigator;
use Windwalker\Core\Router\RouteUri;
use Windwalker\Crypt\Hasher\PasswordHasherInterface;
use Windwalker\ORM\ORM;

/**
 * The ForgetController class.
 */
#[Controller(
    config: __DIR__ . '/forget.config.php'
)]
class ForgetController
{
    use TranslatorTrait;

    public function request(
        AppContext $app,
        RendererService $rendererService,
        UserService $userService,
        Navigator $nav,
        ORM $orm,
        MailerInterface $mailer
    ): RouteUri {
        $email = $app->input('email');

        if (!$email) {
            return $nav->back();
        }

        /** @var User $user */
        $user = $userService->load(['email' => $email]);

        if (!$user) {
            return $nav->self()->layout('complete');
        }

        $token = JWT::encode(
            [
                'email' => $email,
            ],
            $app->getSecret(),
            'HS256'
        );

        $link = $nav->to('forget_confirm', ['token' => $token, 'email' => idn_to_ascii($email)])
            ->var('locale', false)
            ->full();

        $user->setResetToken($token);
        $user->setLastReset('now');

        $orm->updateOne(User::class, $user);

        $name = $user->getName();

        $message = $mailer->createMessage(
            $this->trans('luna.forget.request.mail.subject')
        )
            ->to("{$name} <$email>")
            ->html(
                $rendererService->render(
                    'mail.forget',
                    compact('user', 'email', 'token', 'link')
                )
            )
            ->send();

        return $nav->self()->layout('complete');
    }

    public function confirm(AppContext $app, UserService $userService, Navigator $nav): RouteUri
    {
        $token = $app->input('token');

        $payload = JWT::decode(
            $token,
            new Key($app->getSecret(), 'HS256'),
        );

        $email = $payload->email ?? null;

        if (!$email) {
            throw new ValidateFailException($this->trans('luna.forget.request.message.user.not.found'));
        }

        /** @var User $user */
        $user = $userService->load(['email' => $email]);

        if (!$user) {
            throw new ValidateFailException($this->trans('luna.forget.request.message.user.not.found'));
        }

        if ($user->getResetToken() !== $token) {
            throw new ValidateFailException($this->trans('luna.forget.request.message.invalid.token'));
        }

        $app->getState()->remember('reset.token', $token);

        return $nav->to('forget_reset');
    }

    public function reset(
        AppContext $app,
        UserService $userService,
        ORM $orm,
        Navigator $nav
    ): RouteUri {
        $token = $app->input('token');

        if (!$token) {
            throw new ValidateFailException('No Token');
        }

        $payload = JWT::decode(
            $token,
            new Key($app->getSecret(), 'HS256'),
        );

        $email = $payload->email ?? null;

        /** @var User $user */
        $user = $userService->load(['email' => (string) $email]);

        if (!$user) {
            throw new ValidateFailException($this->trans('luna.forget.request.message.user.not.found'));
        }

        if ($user->getResetToken() !== $token) {
            throw new ValidateFailException($this->trans('luna.forget.request.message.invalid.token'));
        }

        $srpService = $app->retrieve(SRPService::class);

        if ($srpService->isEnabled()) {
            $user = $srpService->handleRegister($app, $user);
        } else {
            $password = $app->input('password');
            $password2 = $app->input('password2');

            $hasher = $app->retrieve(PasswordHasherInterface::class);

            if ($password !== $password2) {
                throw new ValidateFailException($this->trans('luna.forget.reset.message.password.not.match'));
            }

            $user->setPassword($hasher->hash($password));
        }

        $user->setResetToken('');
        $user->setLastReset('now');


        $orm->updateOne(
            User::class,
            $user
        );

        $app->getState()->forget('reset.token');

        return $nav->to('forget_complete');
    }
}
