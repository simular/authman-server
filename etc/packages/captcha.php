<?php

declare(strict_types=1);

use Lyrasoft\Luna\Captcha\CaptchaManager;
use Lyrasoft\Luna\Captcha\NullCaptchaDriver;

return [
    'captcha' => [
        'enabled' => true,

        'default' => env('CAPTCHA_DEFAULT') ?? 'none',

        'listeners' => [
        ],

        'providers' => [
        ],

        'factories' => [
            'instances' => [
                'google' => CaptchaManager::recaptcha(
                    (string) env('RECAPTCHA_KEY'),
                    (string) env('RECAPTCHA_SECRET'),
                    (string) env('RECAPTCHA_TYPE', 'checkbox'),
                ),
                'image' => CaptchaManager::gregwar(),
                'none' => fn () => new NullCaptchaDriver()
            ]
        ]
    ]
];
