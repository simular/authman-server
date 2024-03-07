<?php

declare(strict_types=1);

use Windwalker\Core\Manager\MailerManager;
use Windwalker\Core\Provider\MailerProvider;

return [
    'default' => 'default',

    'from' => env('MAIL_FROM') ?: 'Windwalker <noreply@windwalker.local>',

    'reply_to' => env('MAIL_REPLY_TO'),

    'envelope' => [
        // Must use `new \Symfony\Component\Mime\Address('email', 'name')`
        'sender' => null,
        'recipients' => [],
    ],

    'providers' => [
        MailerProvider::class,
    ],

    'bindings' => [
    ],

    'factories' => [
        'instances' => [
            'default' => fn(MailerManager $manager) => $manager->createMailer(
                [
                    'envelope' => $manager->config('envelope'),
                    'dsn' => env('MAIL_DSN_DEFAULT'),

                    // 'dsn' => [
                    //     'scheme' => env('MAIL_TRANSPORT'),
                    //     'host' => env('MAIL_HOST'),
                    //     'user' => env('MAIL_USER') ?: null,
                    //     'password' => env('MAIL_PASSWORD') ?: null,
                    //     'port' => ((int) env('MAIL_PORT')) ?: null,
                    //     'options' => [
                    //         'verify_peer' => env('MAIL_VERIFY')
                    //     ],
                    // ],

                    // Auto CC to emails, use (,) separate addresses.
                    'cc' => env('MAIL_CC'),

                    // Auto BCC to emails, use (,) separate addresses.
                    'bcc' => env('MAIL_BCC'),
                ]
            ),
        ],
    ],
];
