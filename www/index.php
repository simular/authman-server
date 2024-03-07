<?php

namespace App\Public;

use Windwalker\Core\Application\WebApplication;
use Windwalker\Core\Runtime\Runtime;
use Windwalker\Http\Event\RequestEvent;
use Windwalker\Http\Output\Output;
use Windwalker\Http\Server\HttpServer;

$root = __DIR__ . '/..';

if (!is_file($root . '/vendor/autoload.php')) {
    exit('Please run `composer install` First.');
}

include $root . '/vendor/autoload.php';

include __DIR__ . '/../etc/define.php';

Runtime::ipBlock(['dev'], env('DEV_ALLOW_IPS'));

Runtime::boot(WINDWALKER_ROOT, __DIR__);

Runtime::loadConfig(Runtime::getRootDir() . '/etc/runtime.php');

$container = Runtime::getContainer();

/** @var HttpServer $server */
/** @var WebApplication $app */
$server = $container->resolve('factories.servers.http');
$app = $container->resolve('factories.apps.main');
$app->bootForServer($server);
$server->getEventDispatcher()->addDealer($app->getEventDispatcher());

$server->onRequest(function (RequestEvent $event) use ($app) {
    $event->setResponse($app->executeServerEvent($event));
});

$server->listen();

$app->terminate();
