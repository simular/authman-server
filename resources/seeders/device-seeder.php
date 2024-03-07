<?php

declare(strict_types=1);

namespace App\Seeder;

use App\Entity\Device;
use App\Entity\User;
use App\Enum\DeviceType;
use Windwalker\Core\Seed\Seeder;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;

/**
 * Device Seeder
 *
 * @var Seeder          $seeder
 * @var ORM             $orm
 * @var DatabaseAdapter $db
 */
$seeder->import(
    static function () use ($seeder, $orm, $db) {
        $faker = $seeder->faker('en_US');

        /** @var EntityMapper<Device> $mapper */
        $mapper = $orm->mapper(Device::class);

        $userIds = $orm->findColumn(
            User::class,
            'id',
        );

        $devices = [
            'Mac',
            'PC',
            'iPhone',
            'Android',
            'Tablet'
        ];

        $oss = [
            'Windows',
            'MacOS',
            'iOS',
            'Linux',
            'Android'
        ];

        foreach ($userIds as $userId) {
            foreach (range(1, 3) as $d) {
                $deviceName = $faker->randomElement($devices);

                $device = $mapper->createEntity();
                $device->setUserId($userId);
                $device->setTitle('My ' . $deviceName . ' ' . random_int(1, 5));
                $device->setDevice($deviceName);
                $device->setType($faker->randomElement(DeviceType::cases()));
                $device->setOs($faker->randomElement($oss));
                $device->setUa($faker->userAgent());

                $mapper->createOne($device);

                $seeder->outCounting();
            }
        }
    }
);

$seeder->clear(
    static function () use ($seeder, $orm, $db) {
        //
    }
);
