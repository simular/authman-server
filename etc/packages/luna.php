<?php

declare(strict_types=1);

use Lyrasoft\Luna\LunaPackage;
use Lyrasoft\Luna\Subscriber\AutoOpenGraphSubscriber;
use Lyrasoft\Luna\Subscriber\BuildFormFieldSubscriber;
use Lyrasoft\Luna\Subscriber\EntityBuildingSubscriber;
use Lyrasoft\Luna\Subscriber\LocaleSubscriber;
use Lyrasoft\Luna\User\Handler\SessionDatabaseHandler;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Session\Handler\DatabaseHandler;

return [
    'luna' => [
        'enabled' => true,

        'providers' => [
            LunaPackage::class
        ],

        'listeners' => [
            ConsoleApplication::class => [
                EntityBuildingSubscriber::class,
                BuildFormFieldSubscriber::class,
            ],
            AppContext::class => [
                LocaleSubscriber::class,
                AutoOpenGraphSubscriber::class
            ]
        ],

        'aliases' => [
            DatabaseHandler::class => SessionDatabaseHandler::class
        ],

        'view_extends' => [
            'front' => [
                'base' => 'global.body',
                'auth' => 'global.auth',
                'error' => 'global.body',
            ],

            'admin' => [
                'base' => 'admin.global.body',
                'auth' => 'admin.global.auth',
                'edit' => 'admin.global.body-edit',
                'list' => 'admin.global.body-list',
                'modal' => 'admin.global.pure',
                'error' => 'admin.global.body',
            ]
        ],

        'i18n' => [
            'enabled' => false,
            'uri_prefix' => true,
            'front' => [
                'enabled' => false
            ],
            'admin' => [
                'enabled' => false
            ],
            'types' => [
                //
            ]
        ],

        'error' => [
            'route' => null,
            'layout' => null
        ]
    ]
];
