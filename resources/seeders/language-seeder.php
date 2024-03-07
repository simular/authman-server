<?php

declare(strict_types=1);

namespace App\Seeder;

use Lyrasoft\Luna\Entity\Language;
use Windwalker\Core\Seed\Seeder;
use Windwalker\Database\DatabaseAdapter;
use Windwalker\ORM\ORM;

/**
 * Language Seeder
 *
 * @var Seeder          $seeder
 * @var ORM             $orm
 * @var DatabaseAdapter $db
 */
$seeder->import(
    static function () use ($seeder, $orm, $db) {
        $orm->updateWhere(Language::class, ['state' => 1], ['code' => ['zh-TW', 'ja-JP']]);
    }
);

$seeder->clear(
    static function () use ($seeder, $orm, $db) {
        //
    }
);
