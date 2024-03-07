<?php

declare(strict_types=1);

return [
    'backup' => [
        'providers' => [
            \Lyrasoft\Backup\BackupPackage::class
        ],
        'profiles' => [
            'default' => [
                'dump_database' => true,

                'database' => [
                    'host' => env('DATABASE_HOST'),
                    'user' => env('DATABASE_USER'),
                    'pass' => env('DATABASE_PASSWORD'),
                    'dbname' => env('DATABASE_NAME'),
                ],

                'dump_files' => 0,

                'pattern' => [
                    '/**/*',
                    '!/node_modules/**',
                    '!/vendor/**',
                    '!/.git/**',
                    '!/logs/*',
                    '!/cache/*',
                    '!/tmp/*',
                ],

                'mysqldump_binary' => null,
                'mysqldump_extra' => ''
            ]
        ]
    ]
];
