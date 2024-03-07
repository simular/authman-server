<?php

declare(strict_types=1);

/*
 * --------------------------------------------------------------------------
 * Custom Scripts
 * --------------------------------------------------------------------------
 *
 * Add some commands here to batch execute. Example:
 * 'foo' => [
 *     'git pull'
 *     'composer install'
 *     'php windwalker mig:go'
 *
 * Then just run `$ php windwalker run foo` in terminal.
 *
 */
return [
    // Prepare assets and install dependencies
    'prepare' => [
        'yarn install',
        'yarn build default admin',
    ],

    // Prepare for development and reset migration
    'preparedev' => [
        'cross-env NODE_ENV=development php windwalker run prepare',
        'php windwalker mig:reset --seed -f'
    ],

    // Update code and dependencies
    'update' => [
        'git pull',
        'cross-env COMPOSER_PROCESS_TIMEOUT=600 composer install',
        'cross-env APP_ENV=dev php windwalker mig:go -f',
        'php windwalker run prepare',
    ],

    // Deploy new version
    'deploy' => [
        'git pull',
        'cross-env COMPOSER_PROCESS_TIMEOUT=600 composer install --no-dev',
        'cross-env APP_ENV=dev php windwalker mig:go -f',
        'cross-env NODE_ENV=production php windwalker run prepare',
        'php windwalker asset:version',
    ],
];
