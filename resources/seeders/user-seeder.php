<?php

declare(strict_types=1);

namespace App\Seeder;

use App\Cipher\SimpleSodiumCipher;
use App\Entity\User;
use App\Service\EncryptionService;
use Lyrasoft\Luna\Access\AccessService;
use Lyrasoft\Luna\Auth\SRP\SRPService;
use Lyrasoft\Luna\User\UserService;
use Windwalker\Core\Seed\Seeder;
use Windwalker\Crypt\Hasher\PasswordHasherInterface;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;

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

        $pass = '1234';
        $basicRoles = $accessService->getBasicRoles();
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

            $pf = $srpService->generateVerifier($item->getEmail(), $pass);
            //
            // $a = $client->generateRandomSecret();
            // $A = $client->generatePublic($a);
            //
            // $private = $encryptionService->generateEncryptedPrivateKeyFromUserInfo(
            //     $srpService->getSRPServer(),
            //     $item->getEmail(),
            //     $pf->salt,
            //     $pf->verifier,
            //     $A
            // );

            $item->setPassword($srpService::encodePasswordVerifier($pf->salt, $pf->verifier));

            $item = $mapper->createOne($item);

            $accessService->addRolesToUser($item, $basicRoles);

            $seeder->outCounting();
        }
    }
);

$seeder->clear(
    static function () use ($seeder, $orm, $db) {
        $seeder->truncate(User::class);
    }
);
