<?php

declare(strict_types=1);

namespace App\Module\Api\Auth;

use App\Api\ApiControllerTrait;
use App\Api\ApiEntry;
use App\DTO\UserDTO;
use App\Entity\User;
use App\Enum\ErrorCode;
use App\Service\JwtAuthService;
use Brick\Math\BigInteger;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Lyrasoft\Luna\Auth\SRP\SRPService;
use Lyrasoft\Luna\User\UserService;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\Controller;
use Windwalker\Core\Http\RequestAssert;
use Windwalker\Core\Manager\SessionManager;
use Windwalker\DI\Container;
use Windwalker\ORM\ORM;
use Windwalker\Session\Session;
use Windwalker\SRP\Exception\InvalidSessionProofException;

use function Windwalker\tid;

#[Controller]
class AuthController
{
    use ApiControllerTrait;

    /**
     * @param  AppContext  $app
     *
     * @return  array{ 0: SRPService, 1: Session }
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Windwalker\DI\Exception\DefinitionException
     */
    protected function prepareSRPAndSession(AppContext $app, string $sessId): array
    {
        // Replace Session driver
        $app->getContainer()
            ->bind(
                Session::class,
                fn(Container $container) => $container->get(SessionManager::class)
                    ->get('database')
            );

        $srpService = $app->retrieve(SRPService::class);
        $session = $app->retrieve(Session::class);

        $session->stop();
        $session->setId($sessId);

        return [$srpService, $session];
    }

    protected function releaseSessionDriver(AppContext $app): void
    {
        // Replace Session driver
        $app->getContainer()
            ->bind(
                Session::class,
                fn(Container $container) => $container->get(SessionManager::class)
                    ->get('array')
            );
    }

    public function challenge(
        AppContext $app,
        ORM $orm,
    ): ?array {
        $email = $app->input('email');

        RequestAssert::assert($email, 'No email');

        $sessId = tid();
        [$srpService, $session] = $this->prepareSRPAndSession($app, $sessId);

        $user = $orm->findOne(User::class, compact('email'));

        if (!$user) {
            ErrorCode::INVALID_CREDENTIALS->throw();
        }

        $password = $user->getPassword();

        if (!$srpService::isValidSRPHash($password)) {
            return [
                'salt' => '',
                'B' => '',
            ];
        }

        $pf = $srpService::decodePasswordVerifier($password);

        $e = $srpService->step1($email, $pf->salt, $pf->verifier);

        $sess = JWT::encode(
            [
                'exp' => time() + 10000,
                'sess' => $sessId,
            ],
            $user->getSecret(),
            'HS512'
        );

        $this->releaseSessionDriver($app);

        return [
            'salt' => $pf->salt->toBase(16),
            'B' => $e->public->toBase(16),
            'sess' => $sess,
            'sid' => $session->getId(),
        ];
    }

    #[ApiEntry]
    public function authenticate(
        AppContext $app,
        ORM $orm,
        JwtAuthService $jwtAuthService,
    ): array {
        [$email, $A, $M1, $sess] = $app->input('email', 'A', 'M1', 'sess')->values();

        RequestAssert::assert($email, 'No email');
        RequestAssert::assert($A, 'Invalid credentials');
        RequestAssert::assert($M1, 'Invalid credentials');

        $user = $orm->findOne(User::class, compact('email'));

        if (!$user) {
            ErrorCode::INVALID_CREDENTIALS->throw();
        }

        $payload = JWT::decode(
            $sess,
            new Key($user->getSecret(), 'HS512')
        );

        $password = $user->getPassword();

        $pf = SRPService::decodePasswordVerifier($password);

        $A = BigInteger::fromBase($A, 16);
        $M1 = BigInteger::fromBase($M1, 16);

        try {
            [$srpService, $session] = $this->prepareSRPAndSession($app, $payload->sess);

            $result = $srpService->step2(
                $email,
                $pf->salt,
                $pf->verifier,
                $A,
                $M1
            );

            $user->setPassword('');
            $user->setSecret('');
            $user->setSessCode('');

            // Create JWT Token
            $accessToken = $jwtAuthService->createAccessToken($user);
            $refreshToken = $jwtAuthService->createRefreshToken($user);

            $user = UserDTO::wrap($user);

            $key = $result->key->toBase(16);
            $proof = $result->proof->toBase(16);

            $session->stop();
            $session->destroy();

            $this->releaseSessionDriver($app);

            return compact(
                'key',
                'proof',
                'accessToken',
                'refreshToken',
                'user'
            );
        } catch (InvalidSessionProofException) {
            ErrorCode::INVALID_CREDENTIALS->throw();
        }
    }

    #[ApiEntry]
    public function register(
        AppContext $app,
        ORM $orm,
        UserService $userService,
        JwtAuthService $jwtAuthService,
    ): array {
        [$email, $salt, $verifier] = $app->input('email', 'salt', 'verifier')->values();

        RequestAssert::assert($email, 'No Email');

        $exists = $orm->findOne(User::class, compact('email'));

        if ($exists) {
            ErrorCode::USER_EMAIL_EXISTS->throw();
        }

        $password = SRPService::encodePasswordVerifier($salt, $verifier);

        /** @var User $user */
        $user = $userService->createUserEntity();
        $user->setEmail($email);
        $user->setPassword($password);
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
