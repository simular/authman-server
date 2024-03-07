<?php

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\Luna\Entity\Config;
use Lyrasoft\Luna\Entity\User;
use Windwalker\Core\Seed\Seeder;
use Windwalker\Data\Collection;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;

/**
 * ConfigSeeder
 *
 * @var Seeder          $seeder
 * @var ORM             $orm
 * @var DatabaseAdapter $db
 */
$seeder->import(
    static function () use ($seeder, $orm, $db) {
        $faker   = $seeder->faker();

        /** @var EntityMapper<Config> $mapper */
        $mapper = $orm->mapper(Config::class);
        $userIds = $orm->mapper(User::class)->findColumn('id')->dump();

        $item = $mapper->createEntity();

        $item->setType('core');
        $item->setContent(['ga' => '']);
        $item->setModified($faker->dateTimeThisYear());
        $item->setModifiedBy((int) $faker->randomElement($userIds));

        $mapper->createOne($item);

        $seeder->outCounting();
    }
);

$seeder->clear(
    static function () use ($seeder, $orm, $db) {
        $seeder->truncate(Config::class);
    }
);
