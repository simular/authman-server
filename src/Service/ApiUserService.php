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
            'salt' => '5650da90c28fbddb2c12dd72652cb5dc',
            'kek' => 'hex:eb0d39c41b0c1993d9258472136f0b53',
            'secret' => 'QCxj9oZ8wAE1ch_OQysk7U1q-njXmOiA3r7e4AIuRDgul3rRAQRcZ8enQ1ibb7nUBVQDR5hT2VrFLmTJ688PvHNSVE' .
                'AKD-UH8oFsK4IFheYwmwimqaa5dhWPrMVT2HCpRtULaGvbdtibl2XwWGlpJ0RTbXAgylPMPIqpVNPbSR-x8ZU9Pf8lCT7qmOxF' .
                'bLJklL-XAzI1H68r9xrn',
            'master' => 'q4MjuHW7HhCddYxUpcsHpd9FYvhQdlc85IGO3Cw03LB7RbnlBJJJs6rOo9izB1eOEvKuQzwHepwrnnAcVtWvg2uQTu' .
                'jwJhaU52Qiq86euF5RYSmDQSiuoGpRCjjexvv3JTsEEhkb6l5MoIptAuanJTGqjzGqpwwgzYhp9SjJdQ7zZgIm3cDpSmGihWu_' .
                'ByD9p-4ve-87mEACatkK5Pq6-MIt9b4A9OpKytDG1ddbxN0Suno8KKkjJgSEbOE='
        ];
    }
}
