<?php

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\Luna\Entity\Association;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\Migration;
use Windwalker\Database\Schema\Schema;

/**
 * Migration UP: 2022011618130001_AssociationInit.
 *
 * @var Migration          $mig
 * @var ConsoleApplication $app
 */
$mig->up(
    static function () use ($mig) {
        $mig->createTable(
            Association::class,
            function (Schema $schema) {
                $schema->char('type')->length(50);
                $schema->integer('target_id');
                $schema->varchar('key');
                $schema->char('hash')->length(32);
                $schema->json('params');

                $schema->addIndex('type');
                $schema->addIndex('key');
                $schema->addIndex('target_id');
                $schema->addIndex('hash');
            }
        );
    }
);

/**
 * Migration DOWN.
 */
$mig->down(
    static function () use ($mig) {
        $mig->dropTables(Association::class);
    }
);
