<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Service\ApiUserService;
use App\Service\JwtAuthService;
use Lyrasoft\Luna\User\UserService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Windwalker\Core\Security\Exception\UnauthorizedException;

class ApiAuthMiddleware implements MiddlewareInterface
{
    public function __construct(protected JwtAuthService $jwtAuthService, protected ApiUserService $userService)
    {
        //
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $authHeader = $request->getHeaderLine('Authorization');

        $this->jwtAuthService->extractAccessTokenFromHeader($authHeader, $user);

        if (!$user) {
            throw new UnauthorizedException('User not found.');
        }

        $this->userService->setCurrentUser($user);

        return $handler->handle($request);
    }
}
