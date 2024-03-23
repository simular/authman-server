<?php

declare(strict_types=1);

namespace App\Module\Api;

use App\Attributes\Transaction;
use App\Entity\User;
use App\Entity\UserSecret;
use App\Service\JwtAuthService;
use Brick\Math\BigInteger;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Lyrasoft\Luna\Auth\SRP\SRPService;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\Controller;
use Windwalker\Core\Security\Exception\UnauthorizedException;
use Windwalker\Crypt\Symmetric\CipherInterface;
use Windwalker\ORM\ORM;

use function Windwalker\Query\uuid2bin;
use function Windwalker\tid;
use function Windwalker\uid;

#[Controller]
class PasswordController
{
    public function challenge(
        AppContext $app,
        ORM $orm,
        JwtAuthService $jwtAuthService,
        SRPService $srpService,
    ): array {
        $authHeader = $app->getHeader('Authorization');

        $jwtAuthService->extractAccessTokenFromHeader($authHeader, $user);

        if (!$user) {
            throw new UnauthorizedException('User not found.');
        }

        $email = $user->getEmail();

        [
            $salt,
            $verifier,
        ] = $app->input('salt', 'verifier')->values();

        $userSecret = $orm->mustFindOne(UserSecret::class, ['user_id' => uuid2bin($user->getId())]);

        $sessId = uid();

        // Run SRP step
        $e = $srpService->step1(
            $email,
            BigInteger::fromBase($salt, 16),
            BigInteger::fromBase($verifier, 16)
        );

        $resetToken = JWT::encode(
            [
                'exp' => time() + 10000,
                'sess' => $sessId,
                'b' => $e->secret->toBase(16),
                'B' => $e->public->toBase(16),
            ],
            $userSecret->getDecodedServerSecret(),
            'HS512'
        );

        $sess = JWT::encode(
            [
                'exp' => time() + 10000,
                'sess' => $sessId,
            ],
            $userSecret->getDecodedServerSecret(),
            'HS512'
        );

        $orm->updateBatch(
            User::class,
            ['reset_token' => $resetToken],
            [
                'id' => uuid2bin($user->getId()),
            ]
        );

        return [
            'B' => $e->public->toBase(16),
            'sess' => $sess
        ];
    }

    #[Transaction]
    public function reset(
        AppContext $app,
        ORM $orm,
        SRPService $srpService,
        CipherInterface $cipher,
    ): true {
        [
            $email,
            $salt,
            $verifier,
            $A,
            $M1,
            $sess,
            $encSecret,
            $encMaster
        ] = $app->input(
            'email',
            'salt',
            'verifier',
            'A',
            'M1',
            'sess',
            'encSecret',
            'encMaster',
        )->values();

        /** @var User $user */
        $user = $orm->mustFindOne(User::class, compact('email'));
        $userSecret = $orm->mustFindOne(UserSecret::class, ['user_id' => uuid2bin($user->getId())]);

        $resetToken = $user->getResetToken();

        $resetPayload = JWT::decode(
            $resetToken,
            new Key($userSecret->getDecodedServerSecret(), 'HS512')
        );

        $sessPayload = JWT::decode(
            $sess,
            new Key($userSecret->getDecodedServerSecret(), 'HS512')
        );

        if ($resetPayload->sess !== $sessPayload->sess) {
            throw new UnauthorizedException('Session expired, please redo this action.');
        }

        $server = $srpService->getSRPServer();
        $result = $server->step2(
            $email,
            BigInteger::fromBase($salt, 16),
            BigInteger::fromBase($verifier, 16),
            BigInteger::fromBase($A, 16),
            BigInteger::fromBase($resetPayload->B, 16),
            BigInteger::fromBase($resetPayload->b, 16),
            BigInteger::fromBase($M1, 16),
        );

        $encSecret = $cipher->decrypt($encSecret, $result->preMasterSecret->toBase(10));
        $encMaster = $cipher->decrypt($encMaster, $result->preMasterSecret->toBase(10));

        $userSecret->setSecret($encSecret->get());
        $userSecret->setMaster($encMaster->get());

        $orm->updateOne($userSecret);

        $password = SRPService::encodePasswordVerifier($salt, $verifier);

        $user->setPassword($password);
        $user->setLastReset('now');
        // $user->setResetToken('');

        $orm->updateOne($user);

        return true;
    }
}
