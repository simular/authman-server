<?php

/**
 * Part of eva project.
 *
 * @copyright  Copyright (C) 2022 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

use Lyrasoft\Contact\ContactPackage;

return [
    'contact' => [
        'enabled' => true,

        'receivers' => [
            'main' => [
                'roles' => [
                    'superuser',
                    'manager',
                    'admin',
                ],
                'cc' => [
                    //
                ],
                'bcc' => [
                    //
                ]
            ]
        ],

        'providers' => [
            ContactPackage::class
        ],

        'bindings' => [
            //
        ]
    ]
];
