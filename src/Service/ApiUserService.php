<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\UserSecret;
use Lyrasoft\Luna\User\UserService;
use Windwalker\DI\Attributes\Service;
use Windwalker\Query\Exception\NoResultException;

use function Windwalker\Query\uuid2bin;

#[Service]
class ApiUserService extends UserService
{
    public function getUserSecret(mixed $conditions = null, bool $refresh = false): ?UserSecret
    {
        return $this->once(
            'user.secret.' . json_encode($conditions),
            function () use ($conditions) {
                if ($conditions === null) {
                    $user = $this->getUser();

                    if (!$user->isLogin()) {
                        return null;
                    }

                    $conditions = ['user_id' => uuid2bin($user->getId())];
                }

                return $this->orm->findOne(UserSecret::class, $conditions);
            },
            $refresh
        );
    }

    public function mustGetUserSecret(mixed $conditions = null, bool $refresh = false): UserSecret
    {
        return $this->getUserSecret($conditions, $refresh)
            ?? throw new NoResultException('User secret not found.');
    }

    /**
     * @return  array{ password: string, salt: string, verifier: string, secret: string, master: string }
     */
    public static function getTestSecrets(): array
    {
        return [
            'password' => '1234',
            'salt' => '114733424420548414266050774842127201756',
            'salt_hex' => '5650da90c28fbddb2c12dd72652cb5dc',
            'kek' => 'hex:4674858f1b111e72df11e3c00a46c549a0eb4b8b108b4934a3294c05b95f1925',
            'secret_hex' => '7d5cb4c5a3fb5f00c30f5289bd1e688a',
            'secret' => 'CJDpDbvpXVxy2aRhLzgUDYsQzFfycZp0_l0IBLIDVoAx8Y-dTkDx04SDTtSGIzweyx9m6VbPaKu610aGMUpHbCRFt2eE9u3SGQuLMzIIRjZIoYEjlhBI-RlokcWJxnngoEFhB7PfGD-LT6m9dAqaZeecg8GDCx_m8MVVS0Qq-l0IAD_9mGkktA==',
            'master' => 'YwZfnMVVjkKV8x8WNTKjR17DDWHvYUEmBFMO4jJ5ZO-XJGFcZMayMtYVrJE_6Q34EVxmH8mppLN5Y-gYd8xmeyXN61BM__8xdYmTwDdaKnSZwozsHEB8myT1k1Hh9Kml_GBcYpuDaZN7z_2LLqwjohYbQXcpknFxMwwz-mFST4L2gU1Xj-7r9mEjnqkkbWFM54kt5Cfd36c=',
        ];
    }
}
