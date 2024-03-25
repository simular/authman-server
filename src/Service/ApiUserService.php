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
     * @return  array{
     *     password: string,
     *     salt: string,
     *     verifier: string,
     *     secret_hex: string,
     *     secret: string,
     *     master: string
     * }
     * @throws \JsonException
     */
    public static function getTestSecrets(): array
    {
        return json_decode(
            file_get_contents(static::getTestSecretsFilePath()),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }

    public static function getTestSecretsFilePath(): string
    {
        return WINDWALKER_SEEDERS . '/data/test-secrets.json';
    }
}
