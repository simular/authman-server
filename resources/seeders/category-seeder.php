<?php

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\Luna\Entity\Category;
use Lyrasoft\Luna\Entity\User;
use Lyrasoft\Luna\Services\LocaleService;
use Unicorn\Enum\BasicState;
use Unicorn\Utilities\SlugHelper;
use Windwalker\Core\Seed\Seeder;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\Nested\Position;
use Windwalker\ORM\NestedSetMapper;
use Windwalker\ORM\ORM;
use Windwalker\Utilities\Utf8String;

/**
 * Category Seeder
 *
 * @var Seeder          $seeder
 * @var ORM             $orm
 * @var DatabaseAdapter $db
 */
$seeder->import(
    static function () use ($seeder, $orm, $db) {
        $types = [
            'article' => [
                'max_level' => 3,
                'number' => 30
            ],
            'portfolio' => [
                'max_level' => 1,
                'number' => 7
            ],
            'member' => [
                'max_level' => 1,
                'number' => 7
            ],
            'banner' => [
                'max_level' => 1,
                'number' => 3
            ],
        ];

        /** @var NestedSetMapper<Category> $mapper */
        $mapper = $orm->mapper(Category::class);

        $faker = $seeder->faker('en_US');

        /** @var NestedSetMapper<Category> $mapper */
        $mapper = $orm->mapper(Category::class);
        $langCodes = LocaleService::getSeederLangCodes($orm);
        $userIds = $orm->findColumn(User::class, 'id')->dump();

        $existsRecordIds = [];

        foreach ($types as $type => $detail) {
            $maxLevel = $detail['max_level'];

            $existsRecordIds[$type] = [1];

            foreach (range(1, $detail['number']) as $i) {
                $langCode = $faker->randomElement($langCodes);
                /** @var Category $item */
                $item = $mapper->createEntity();

                $faker = $seeder->faker($langCode);

                $item->setType($type);
                $item->setTitle(Utf8String::ucwords($faker->sentence(2)));
                $item->setAlias(SlugHelper::safe($item->getTitle()));
                $item->setDescription($faker->paragraph(5));
                $item->setImage($faker->unsplashImage(800, 600));
                $item->setState(BasicState::from($faker->randomElement([1, 1, 1, 0])));
                $item->setLanguage($langCode);
                $item->setCreated($created = $faker->dateTimeThisYear());
                $item->setModified($created->modify('+10days'));
                $item->setCreatedBy((int) $faker->randomElement($userIds));

                $mapper->setPosition(
                    $item,
                    $faker->randomElement($existsRecordIds[$item->getType()]),
                    Position::LAST_CHILD
                );

                /** @var Category $item */
                $item = $mapper->createOne($item);

                if ($item->getLevel() < $maxLevel) {
                    $existsRecordIds[$item->getType()][] = $item->getId();
                }

                $seeder->outCounting();
            }
        }
    }
);

$seeder->clear(
    static function () use ($seeder, $orm, $db) {
        $seeder->truncate(Category::class);
    }
);
