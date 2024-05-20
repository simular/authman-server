<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Entity\User;
use App\Enum\ApiTokenType;
use App\Enum\ErrorCode;
use App\Service\ApiUserService;
use App\Service\JwtAuthService;
use Lyrasoft\Luna\User\UserService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Windwalker\Core\Security\Exception\UnauthorizedException;
use Windwalker\Query\Exception\NoResultException;

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

        if ($authHeader) {
            try {
                $this->jwtAuthService->extractAccessTokenFromHeader($authHeader, $user);
            } catch (NoResultException) {
                ErrorCode::USER_NOT_FOUND->throw();
            }

            $this->checkLastReset($request, $user);

            $this->userService->setCurrentUser($user);
        }

        return $handler->handle($request);
    }

    /**
     * @param  ServerRequestInterface  $request
     * @param  User|null   $user
     *
     * @return  void
     */
    protected function checkLastReset(ServerRequestInterface $request, ?User $user): void
    {
        $clientLastReset = $request->getHeaderLine('X-Password-Last-Reset');

        $serverLastReset = (string) $user->getLastReset()?->toUnix();

        if ($clientLastReset !== $serverLastReset) {
            ErrorCode::PASSWORD_CHANGED->throw();
        }
    }
}
