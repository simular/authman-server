<?php

declare(strict_types=1);

namespace App\Module\Front\Auth\Form;

use Windwalker\Core\Attributes\Ref;
use Windwalker\Core\Language\TranslatorTrait;
use Windwalker\Form\Field\EmailField;
use Windwalker\Form\Field\PasswordField;
use Windwalker\Form\Field\TextField;
use Windwalker\Form\FieldDefinitionInterface;
use Windwalker\Form\Form;

/**
 * The LoginForm class.
 */
class LoginForm implements FieldDefinitionInterface
{
    use TranslatorTrait;

    public function __construct(
        #[Ref('user')]
        protected array $config
    ) {
        //
    }

    public function define(Form $form): void
    {
        $this->useLangNamespace('luna.');

        $form->ns('user', function (Form $form) {
            $loginName = $this->config['login_name'] ?? 'username';

            if ($loginName === 'email') {
                $form->add('email', EmailField::class)
                    ->label($this->trans('user.field.email'))
                    ->attr('data-input-identity', true)
                    ->required(true);
            } else {
                $form->add($loginName, TextField::class)
                    ->label($this->trans('user.field.' . $loginName))
                    ->attr('data-input-identity', true)
                    ->required(true);
            }

            $form->add('password', PasswordField::class)
                ->label($this->trans('user.field.password'))
                ->attr('data-input-password', true)
                ->required(true);
        });
    }
}
