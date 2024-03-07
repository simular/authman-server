<?php

declare(strict_types=1);

use Lyrasoft\Luna\Access\AccessService;

use function Lyrasoft\Luna\create_role;

return [
    'access' => [
        'enabled' => true,

        'selectable_roles' => [
            'member',
            'admin',
            'superuser',
        ],

        'basic_roles' => [
            'member'
        ],

        'roles_db_enabled' => false,
        'roles' => static fn () => [
            'superuser' => create_role(
                'Super User',
            ),
            'public' => create_role(
                'Public',
                children: [
                    'member' => create_role(
                        'Member',
                        children: [
                            'manager' => create_role(
                                'Manager',
                                children: [
                                    'admin' => create_role(
                                        'Admin',
                                    ),
                                ]
                            ),
                        ]
                    ),
                ]
            ),
        ],

        'actions_db_enabled' => false,
        'actions' => [
            AccessService::ADMIN_ACCESS_ACTION => [
                'manager' => true,
            ],

            AccessService::SUPERUSER_ACTION => [
                'superuser' => true,
            ],

            AccessService::ROLE_MODIFY_ACTION => [
                'admin' => true,
            ],

            'create' => [
                'manager' => true,
            ],

            'edit' => [
                'manager' => true,
            ],

            'delete' => [
                'manager' => true,
            ],
        ],
    ],
];
