<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Windwalker\Core\Application\AppContext;

class ApiMiddleware implements MiddlewareInterface
{
    public function __construct(protected AppContext $app)
    {
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = $this->app->getSystemUri()->route;

        if (!str_starts_with($route, 'api/')) {
            return $handler->handle($request);
        }

        $this->app->config->setDeep('session.default', 'array');

        return $handler->handle($request);
    }
}
