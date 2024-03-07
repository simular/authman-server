<?php

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\Luna\Entity\Article;
use Lyrasoft\Luna\Entity\Category;
use Lyrasoft\Luna\Entity\Menu;
use Lyrasoft\Luna\Enum\MenuTarget;
use Windwalker\Core\Seed\Seeder;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\Nested\Position;
use Windwalker\ORM\NestedSetMapper;
use Windwalker\ORM\ORM;

$types = [
    'mainmenu' => [
        'max_level' => 3,
        'number' => 30,
    ],
];

/**
 * Menu Seeder
 *
 * @var Seeder $seeder
 * @var ORM $orm
 * @var DatabaseAdapter $db
 */
$seeder->import(
    static function () use ($seeder, $orm, $db, $types) {
        $faker = $seeder->faker('zh_TW');

        /** @var NestedSetMapper $mapper */
        $mapper = $orm->mapper(Menu::class);
        $articles = $orm->findList(Article::class, ['state' => 1])->all()->dump();
        $categories = $orm->findList(Category::class, ['type' => 'article'])->all()->dump();

        $existsMenuIds = [];
        $views = [
            'article',
            'article_category',
        ];

        foreach ($types as $type => $detail) {
            $maxLevel = $detail['max_level'];

            $existsMenuIds[$type] = [1];

            foreach (range(1, $detail['number']) as $i) {
                /** @var Menu $item */
                $item = $mapper->createEntity();

                $view = $faker->randomElement($views);

                $item->setTitle($faker->sentence(2));
                $item->setType($type);
                $item->setView($view);

                switch ($view) {
                    case 'article':
                        /** @var Article $article */
                        $article = $faker->randomElement($articles);
                        $item->setVariables(
                            [
                                'id' => $article->getId(),
                                'alias' => $article->getAlias(),
                            ]
                        );
                        break;

                    case 'article_category':
                        /** @var Category $category */
                        $category = $faker->randomElement($categories);

                        $item->setVariables(
                            [
                                'id' => $category->getId(),
                                'path' => $category->getPath(),
                            ]
                        );
                        break;
                }

                $item->setImage($faker->unsplashImage());
                $item->setState(1);
                $item->setTarget(MenuTarget::SELF());
                $item->setCreated($faker->dateTimeThisYear);
                $item->setModified($item->getCreated()->modify('+5days'));

                $mapper->setPosition(
                    $item,
                    $faker->randomElement($existsMenuIds[$item->getType()]),
                    Position::LAST_CHILD
                );

                /** @var Menu $item */
                $item = $mapper->createOne($item);

                if ($item->getLevel() < $maxLevel) {
                    $existsMenuIds[$item->getType()][] = $item->getId();
                }

                $seeder->outCounting();
            }
        }
    }
);

$seeder->clear(
    static function () use ($seeder, $orm, $db) {
        $seeder->truncate(Menu::class);
    }
);
