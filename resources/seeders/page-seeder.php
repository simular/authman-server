<?php

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\Luna\Entity\Page;
use Lyrasoft\Luna\Entity\User;
use Windwalker\Core\Seed\Seeder;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;

/**
 * Page Seeder
 *
 * @var Seeder          $seeder
 * @var ORM             $orm
 * @var DatabaseAdapter $db
 */
$seeder->import(
    static function () use ($seeder, $orm, $db) {
        $faker = $seeder->faker('zh_TW');

        $userIds = $orm->findColumn(User::class, 'id', [])->dump();
        /** @var EntityMapper<Page> $mapper */
        $mapper = $orm->mapper(Page::class);

        $content = json_decode(file_get_contents(__DIR__ . '/data/page.json'), true);

        foreach (range(1, 50) as $i) {
            $item = $mapper->createEntity();

            $item->setExtends('global.body');
            $item->setTitle($faker->sentence(2));
            $item->setImage($faker->unsplashImage(800, 600));
            $item->setContent($content);
            $item->setState(1);
            $item->setOrdering($i);
            $item->setCreated($created = $faker->dateTimeThisYear);
            $item->setModified($created->modify('+1month'));
            $item->setCreatedBy((int) $faker->randomElement($userIds));
            $item->setModifiedBy((int) $faker->randomElement($userIds));

            $mapper->createOne($item);

            $seeder->outCounting();
        }
    }
);

$seeder->clear(
    static function () use ($seeder, $orm, $db) {
        $seeder->truncate(Page::class);
    }
);
