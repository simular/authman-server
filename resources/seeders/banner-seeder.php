<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2022 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\Banner\Entity\Banner;
use Lyrasoft\Banner\Enum\BannerVideoType;
use Lyrasoft\Banner\Service\BannerService;
use Lyrasoft\Luna\Entity\Category;
use Lyrasoft\Luna\Entity\User;
use Windwalker\Core\Seed\Seeder;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;
use Windwalker\Utilities\Enum\EnumTranslatableInterface;

use function Windwalker\value;

/**
 * Banner Seeder
 *
 * @var Seeder          $seeder
 * @var ORM             $orm
 * @var DatabaseAdapter $db
 */
$seeder->import(
    static function (BannerService $bannerService) use ($seeder, $orm, $db) {
        $faker = $seeder->faker('zh_TW');

        /** @var EntityMapper<Banner> $mapper */
        $mapper = $orm->mapper(Banner::class);
        // $langCodes = LocaleService::getSeederLangCodes($orm);
        $categoryIds = $orm->findColumn(Category::class, 'id', ['type' => 'banner'])->dump();
        $userIds = $orm->findColumn(User::class, 'id')->dump();
        /** @var EnumTranslatableInterface $typeEnum */
        $typeEnum = $bannerService->getTypeEnum();

        $mediaTypes = ['image', 'video'];

        foreach (range(1, 15) as $i) {
            $item = $mapper->createEntity();

            $item->setTitle($faker->sentence(2));
            $item->setSubtitle($faker->sentence(4));
            $item->setDescription($faker->sentence(7));

            if ($typeEnum) {
                $item->setType(
                    value($faker->randomElement($typeEnum::values()))
                );
            } else {
                $item->setCategoryId((int) $faker->randomElement($categoryIds));
            }

            $mediaType = $faker->randomElement($mediaTypes);

            if ($mediaType === 'video') {
                /** @var BannerVideoType $videoType */
                $videoType = $faker->randomElement(BannerVideoType::values());
                $item->setVideoType($videoType);

                if ($videoType->equals(BannerVideoType::EMBED())) {
                    $item->setVideo('https://www.youtube.com/watch?v=jfKfPfyJRdk');
                    $item->setMobileVideo('https://www.youtube.com/watch?v=rUxyKA_-grg');
                } else {
                    $item->setVideo('https://lyratest.ap-south-1.linodeobjects.com/video/landscape-1080.mp4');
                    $item->setMobileVideo('https://lyratest.ap-south-1.linodeobjects.com/video/mobile-720.mp4');
                }
            }

            $item->setImage($faker->unsplashImage(1920, 800));
            $item->setMobileImage($faker->unsplashImage(720, 720));
            $item->setLink('https://simular.co');

            $item->setLanguage('*');
            $item->setState(1);
            $item->setOrdering($i);
            $item->setCreated($faker->dateTimeThisYear());
            $item->setModified($item->getCreated()->modify('+10days'));
            $item->setCreatedBy((int) $faker->randomElement($userIds));

            $mapper->createOne($item);
        }
    }
);

$seeder->clear(
    static function () use ($seeder, $orm, $db) {
        $seeder->truncate(Banner::class);
    }
);
