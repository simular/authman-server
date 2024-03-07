<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2022 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\Contact\Entity\Contact;
use Lyrasoft\Contact\Enum\ContactState;
use Lyrasoft\Luna\Entity\User;
use Windwalker\Core\Seed\Seeder;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;

/**
 * Contact Seeder
 *
 * @var Seeder          $seeder
 * @var ORM             $orm
 * @var DatabaseAdapter $db
 */
$seeder->import(
    static function () use ($seeder, $orm, $db) {
        $faker = $seeder->faker('en_US');

        /** @var EntityMapper<Contact> $mapper */
        $mapper = $orm->mapper(Contact::class);
        $userIds = $orm->findColumn(User::class, 'id')->dump();

        foreach (range(1, 50) as $i) {
            $item = $mapper->createEntity();

            $item->setTitle($faker->sentence(2));
            $item->setType('main');
            $item->setName($faker->name());
            $item->setEmail($faker->safeEmail());
            $item->setPhone($faker->phoneNumber());
            $item->setState($faker->randomElement(ContactState::values()));
            $item->setUrl($faker->url());
            $item->setContent($faker->paragraph(10));
            $item->setAssigneeId((int) $faker->randomElement($userIds));
            $item->setCreated($faker->dateTimeThisYear());
            $item->setDetails(
                [
                    'address' => $faker->streetAddress(),
                    'zip' => $faker->postcode()
                ]
            );

            $mapper->createOne($item);

            $seeder->outCounting();
        }
    }
);

$seeder->clear(
    static function () use ($seeder, $orm, $db) {
        $seeder->truncate(Contact::class);
    }
);
