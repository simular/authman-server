<?php

declare(strict_types=1);

return [
    'debugger' => [
        'enabled' => env('APP_ENV') === 'dev',

        'editor' => env('DEBUGGER_EDITOR', 'phpstorm'),

        'profiler_disabled' => false,

        'listeners' => [
            \Windwalker\Core\Application\AppContext::class => [
                \Windwalker\Debugger\Subscriber\DebuggerSubscriber::class
            ]
        ],

        'providers' => [
            \Windwalker\Debugger\DebuggerPackage::class
        ],

        'cache' => [
            'max_files' => 100
        ]
    ]
];
