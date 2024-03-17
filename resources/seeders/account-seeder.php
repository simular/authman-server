<?php

declare(strict_types=1);

namespace App\Seeder;

use App\Entity\Account;
use App\Entity\Device;
use App\Entity\User;
use App\Enum\DeviceType;
use Windwalker\Core\Seed\Seeder;
use Windwalker\Crypt\Hasher\PasswordHasher;
use Windwalker\Crypt\Symmetric\CipherInterface;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;

/**
 * Account Seeder
 *
 * @var Seeder          $seeder
 * @var ORM             $orm
 * @var DatabaseAdapter $db
 */
$seeder->import(
    static function (CipherInterface $cipher) use ($seeder, $orm, $db) {
        $faker = $seeder->faker('en_US');

        /** @var EntityMapper<Account> $mapper */
        $mapper = $orm->mapper(Account::class);
        $users = $orm->findList(User::class)->all();

        $secrets = [];

        /** @var User $user */
        foreach ($users as $user) {
            foreach (range(1, 12) as $i) {
                $item = $mapper->createEntity();

                $item->setUserId($user->getId());
                $item->setIcon($faker->unsplashImage(150, 150));
                $item->setTitle($faker->sentence());
                $item->setUrl($faker->url());
                $item->setSecret(PasswordHasher::genRandomPassword(12));

                $account = $mapper->createOne($item);

                $seeder->outCounting();
            }
        }
    }
);

$seeder->clear(
    static function () use ($seeder, $orm, $db) {
        $seeder->truncate(Account::class);
    }
);
