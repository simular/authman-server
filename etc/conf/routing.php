<?php

declare(strict_types=1);

use Windwalker\Core\Provider\RouterProvider;

return [
    'routes' => [
        __DIR__ . '/../../routes/main.route.php'
    ],

    'providers' => [
        RouterProvider::class
    ],

    'bindings' => [
        //
    ],

    'extends' => [
        //
    ]
];
