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
            'verifier' => '8383e64b9ac360213ea5387f708a16e492859cea6539c9dd3b2d0b81f6890350a79764c631026c7c74ba080a3' .
                'b3b502deea889c7ffaaecb338ce9a241cc45c168b49f9ed6f83531e70ad2b464635a8d432ef36e0f3fced525d58f158ed21' .
                'd075dc531884f0a09306ea1fb925e194bcd2efd508179a8a58c65b90f544e72369fed2b41eae99b42020ab11eccbc56ef47' .
                'f6069191962ee49e2f91b6aa84a8ca1255e9dcebefb6a89057cf6be3aaa8b87128ad8ceac493ccf9298a24a1435d32e7f23' .
                'e5ae026c8e663d3b7ace189e2f82e80b9cd9154324e0c591318b4d7c9a0b5b6c9e3e3a782bd31ff40bb9f230159a1c7168f' .
                '29fb23f8c0582bd4b2ed8bb2a71',
            'secret' => 'Zu9mUqoYuopjVhPpPRXRtPf7H1eplYcSQe1HysSfPY8yMekk190EOLEpbVU8qqYThI3p7Cn8KAAQ7blkNSuFmvAhDncd' .
                'AronX_rwyWUoIAIHQIjE1tB_6nG7H9Srnq9b0Czpt-4hXkXZ_sImY9Zk05JKR0vnvfrD-h1f8U2H8a-LgAs4PtKmUw==',
            'master' => 'b9SFx-l5liQVRyc8QTQeFdOtZWsXPoLxUGyZrKVU6nPPpnavR5QcyfWl_u5_fJVzQ7Nf23TR0g4emtYtgtIm' .
                '-olcZRy7cLQRAM9hgksc9h1qmmmYsVgsSVle6VrTfS_8h4nuT9YCfhXB7zGvOoBW8lN9oNAskRp_ogyhXsZ_' .
                'IYDSqATaFK0AcMwrG2glLWP-g-3pYeYTPlo='
        ];
    }
}
