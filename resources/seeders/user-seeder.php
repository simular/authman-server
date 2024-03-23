<?php

declare(strict_types=1);

namespace App\Seeder;

use App\Entity\User;
use App\Entity\UserSecret;
use App\Entity\UserSocial;
use App\Service\ApiUserService;
use App\Service\EncryptionService;
use Brick\Math\BigInteger;
use Lyrasoft\Luna\Access\AccessService;
use Lyrasoft\Luna\Auth\SRP\SRPService;
use Lyrasoft\Luna\User\UserService;
use Windwalker\Core\Seed\Seeder;
use Windwalker\Crypt\Hasher\PasswordHasherInterface;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;
use Windwalker\SRP\Step\PasswordFile;

/**
 * User Seeder
 *
 * @var Seeder          $seeder
 * @var ORM             $orm
 * @var DatabaseAdapter $db
 */
$seeder->import(
    static function (
        PasswordHasherInterface $password,
        UserService $userService,
        AccessService $accessService,
        SRPService $srpService,
        EncryptionService $encryptionService,
    ) use (
        $seeder,
        $orm,
        $db
    ) {
        $faker = $seeder->faker('zh_TW');

        /** @var EntityMapper<User> $mapper */
        $mapper = $orm->mapper(User::class);

        $basicRoles = $accessService->getBasicRoles();

        $secrets = ApiUserService::getTestSecrets();
        $pass = $secrets['password'];
        $salt = BigInteger::fromBase($secrets['salt_hex'], 16);
        $secret = $secrets['secret'];
        $master = $secrets['master'];

        $client = $srpService->getSRPClient();

        foreach (range(1, 50) as $i) {
            $item = $mapper->createEntity();

            $item->setName($faker->name());
            $item->setEmail($faker->safeEmail());
            $item->setAvatar($faker->avatar(400));
            $item->setEnabled((bool) $faker->randomElement([1, 1, 1, 0]));
            $item->setVerified(true);
            $item->setLastLogin($faker->dateTimeThisYear());
            $item->setRegistered($faker->dateTimeThisYear());

            $x = $client->generatePasswordHash(
                $salt,
                $item->getEmail(),
                $pass
            );

            // (g^x % N)
            $verifier = $client->generateVerifier($x);

            $pf = new PasswordFile($salt, $verifier);

            $item->setPassword(
                $srpService::encodePasswordVerifier($pf->salt, $pf->verifier)
            );

            $item = $mapper->createOne($item);

            $userSecret = $item->getSecretEntity(true);
            $userSecret->setSecret($secret);
            $userSecret->setMaster($master);

            $orm->updateOne($userSecret);

            $accessService->addRolesToUser($item, $basicRoles);

            $seeder->outCounting();
        }
    }
);

$seeder->clear(
    static function () use ($seeder, $orm, $db) {
        $seeder->truncate(User::class, UserSecret::class, UserSocial::class);
    }
);
