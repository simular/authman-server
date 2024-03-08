<?php

declare(strict_types=1);

namespace App\Module\Api\Auth;

use App\Api\ApiControllerTrait;
use App\Api\ApiEntry;
use App\DTO\UserDTO;
use App\Entity\User;
use App\Service\JwtAuthService;
use Lyrasoft\Luna\User\UserService;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\Controller;
use Windwalker\Core\Http\RequestAssert;
use Windwalker\Core\Security\Exception\UnauthorizedException;
use Windwalker\Crypt\Hasher\PasswordHasherInterface;
use Windwalker\ORM\ORM;

#[Controller]
class AuthController
{
    use ApiControllerTrait;

    #[ApiEntry]
    public function authenticate(
        AppContext $app,
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

        /** @var User $user */
        $user = $userService->mustLoad(compact('email'));

        $user->setPassword('');
        $user->setSecret('');
        $user->setSessCode('');

        // Create JWT Token
        $accessToken = $jwtAuthService->createAccessToken($user);
        $refreshToken = $jwtAuthService->createRefreshToken($user);

        $user = UserDTO::wrap($user);

        return compact(
            'accessToken',
            'refreshToken',
            'user'
        );
    }

    public function register(
        AppContext $app,
        ORM $orm,
        UserService $userService,
        PasswordHasherInterface $hasher,
        JwtAuthService $jwtAuthService
    ): array {
        [$email, $password] = $app->input('email', 'password')->values();

        RequestAssert::assert($email, 'No Email');
        RequestAssert::assert($password, 'No Password');

        /** @var User $user */
        $user = $userService->createUserEntity();
        $user->setEmail($email);
        $user->setPassword($hasher->hash($password));
        $user->setRegistered('now');
        $user->setEnabled(true);
        $user->setVerified(true);

        $user = $orm->createOne($user);

        // Create JWT Token
        $accessToken = $jwtAuthService->createAccessToken($user);
        $refreshToken = $jwtAuthService->createRefreshToken($user);

        $user = UserDTO::wrap($user);

        return compact(
            'accessToken',
            'refreshToken',
            'user'
        );
    }
}
