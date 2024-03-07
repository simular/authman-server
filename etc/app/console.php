<?php

declare(strict_types=1);

return array_merge(
    require __DIR__ . '/windwalker.php',
    [
        'commands' => include __DIR__ . '/../../resources/registry/commands.php',

        'listeners' => [

        ],

        'schedules' => [
            __DIR__ . '/../../resources/registry/schedules.php'
        ],

        'scripts' => include __DIR__ . '/../../resources/registry/scripts.php',

        'generator' => [
            'commands' => include __DIR__ . '/../../resources/registry/generator.php'
        ],

        'web_simulator' => [
            'uri' => env('WEB_SIMULATOR_URI'),
            'docroot' => env('WEB_SIMULATOR_DOCROOT'),
        ]
    ],
    require __DIR__ . '/../config.php'
);
