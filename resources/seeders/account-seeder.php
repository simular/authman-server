<?php

declare(strict_types=1);

namespace App\Seeder;

use App\Entity\Account;
use App\Entity\Device;
use App\Entity\User;
use App\Enum\DeviceType;
use App\Service\ApiUserService;
use App\Service\EncryptionService;
use OTPHP\TOTP;
use Unicorn\Flysystem\Base64DataUri;
use Windwalker\Core\Seed\Seeder;
use Windwalker\Crypt\Hasher\PasswordHasher;
use Windwalker\Crypt\SecretToolkit;
use Windwalker\Crypt\Symmetric\CipherInterface;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;

use function Symfony\Component\String\s;

/**
 * Account Seeder
 *
 * @var Seeder          $seeder
 * @var ORM             $orm
 * @var DatabaseAdapter $db
 */
$seeder->import(
    static function (CipherInterface $cipher, EncryptionService $encryptionService) use ($seeder, $orm, $db) {
        $faker = $seeder->faker('en_US');

        /** @var EntityMapper<Account> $mapper */
        $mapper = $orm->mapper(Account::class);
        $users = $orm->findList(User::class)->all();

        $master = $encryptionService->getTestMasterKey();

        /** @var User $user */
        foreach ($users as $user) {
            foreach (range(1, 12) as $i) {
                $item = $mapper->createEntity();

                $totp = TOTP::generate();
                $totpSecret = $totp->getSecret();

                $content = [
                    'title' => $faker->sentence(),
                    'secret' => $totpSecret,
                    'url' => $faker->url()
                ];

                $contentJson = json_encode($content);
                $encContent = $cipher->encrypt($contentJson, $master);

                $item->setContent($encContent);
                $item->setUserId($user->getId());
                $item->setImage('dev://test-image');

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
