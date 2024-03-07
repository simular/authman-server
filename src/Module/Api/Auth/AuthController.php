<?php

declare(strict_types=1);

namespace App\Module\Api\Auth;

use App\Service\JwtAuthService;
use Lyrasoft\Luna\User\UserService;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\Controller;
use Windwalker\Core\Security\Exception\UnauthorizedException;
use Windwalker\ORM\ORM;

#[Controller]
class AuthController
{
    public function authenticate(
        AppContext $app,
        ORM $orm,
        UserService $userService,
        JwtAuthService $jwtAuthService
    ): array {
        [$email, $password] = JwtAuthService::extractBasicAuth(
            $app->getAppRequest()->getServerRequest()->getHeaderLine('authorization')
        );

        $result = $userService->authenticate(compact('email', 'password'));

        if (!$result) {
            throw new UnauthorizedException('帳號或密碼不符合', 401);
        }

        $user = $userService->mustLoad(compact('email'));

        $user->setPassword('');

        // Create JWT Token
        $accessToken = $jwtAuthService->createAccessToken($user);
        $refreshToken = $jwtAuthService->createRefreshToken($user);

        return compact(
            'accessToken',
            'refreshToken',
            'user'
        );
    }
}
