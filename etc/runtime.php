<?php

use App\Console\Application as ConsoleApplication;
use App\Web\Application as WebApplication;
use Windwalker\DI\Container;
use Windwalker\Http\Server\PhpServer;

use function Windwalker\DI\create;
use function Windwalker\ref;

return [
    'factories' => [
        'servers' => [
            'http' => ref('di.servers.http'),
        ],
        'apps' => [
            'main' => ref('di.apps.main'),
        ],
        'console' => ref('di.console'),
    ],

    'di' => [
        'servers' => [
            'http' => create(
                PhpServer::class
            ),
        ],
        'apps' => [
            'main' => create(
                static function (Container $container) {
                    $app = new WebApplication($container->createChild());
                    $app->loadConfig(__DIR__ . '/app/main.php');

                    return $app;
                }
            ),
        ],
        'console' => static function (Container $container) {
            $console = new ConsoleApplication($container->createChild());
            $console->setAutoExit(false);
            $console->loadConfig(__DIR__ . '/app/console.php');
            $console->boot();
            return $console;
        }
    ],
    'ini' => [
        //
    ],
    '@bin' => WINDWALKER_BIN,
    '@cache' => WINDWALKER_CACHE,
    '@etc' => WINDWALKER_ETC,
    '@languages' => WINDWALKER_LANGUAGES,
    '@logs' => WINDWALKER_LOGS,
    '@migrations' => WINDWALKER_MIGRATIONS,
    '@public' => WINDWALKER_PUBLIC,
    '@resources' => WINDWALKER_RESOURCES,
    '@root' => WINDWALKER_ROOT,
    '@routes' => WINDWALKER_ROUTES,
    '@seeders' => WINDWALKER_SEEDERS,
    '@source' => WINDWALKER_SOURCE,
    '@servers' => WINDWALKER_SERVERS,
    '@temp' => WINDWALKER_TEMP,
    '@vendor' => WINDWALKER_VENDOR,
    '@views' => WINDWALKER_VIEWS,
];
