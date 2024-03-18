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
            'kek' => 'hex:eb0d39c41b0c1993d9258472136f0b532a1eb731340fadc2a45446cb94b3f1cd',
            'secret' => 'DlMx8Asm-UHPgQHOOf4O4uToYrLU4rZ1bvkWCKaIID8RCyPcwiVXdO8kZBTUXtuHDM0z4JhhflMBH4ycobdY4Z7lclutbov4U5Bb0kwZuHpynsoCK6nJXj5NjaLEtgwUYLZoEwoHJ_qDN_W76Qr1haS97KFpsCfR_1KCVW_jGWmCXApfDY4kkw==',
            'master' => 'NvpLnuUpf8awYgsUsgw9WZC9r6ryQhxcHho9rpjVsl2wfKNot7l9DGY73aBLQIe-OVRSP6gFScwPzRoVllioBunEi_qbe8DQxObyWOyBEx_pCVVdKOyRlTl-PC6AaT5VxDIVpeKU6XrhXnoUOeCpY2yTbAV2VNIfAX13Cv_q_qUdBQLiXd7hFFiXoCe8aWPwD_ix7cIW04M='
        ];
    }
}
