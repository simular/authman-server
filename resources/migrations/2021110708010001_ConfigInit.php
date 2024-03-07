<?php

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\Luna\Entity\Config;
use Windwalker\Core\Migration\Migration;
use Windwalker\Database\Schema\Schema;

/**
 * Migration UP: 2021110708010001_ConfigInit.
 *
 * @var Migration $mig
 */
$mig->up(
    static function () use ($mig) {
        $mig->createTable(Config::class, function (Schema $schema) {
            $schema->char('type')->length(50)->comment('Type');
            $schema->char('subtype')->length(50)->comment('Sub Type');
            $schema->json('content')->comment('Content');
            $schema->datetime('modified')->nullable(true)->comment('Modified');
            $schema->integer('modified_by')->comment('Modified User');

            $schema->addIndex('type');
            $schema->addIndex('subtype');
            $schema->addPrimaryKey(['type', 'subtype']);
        });
    }
);

/**
 * Migration DOWN.
 */
$mig->down(
    static function () use ($mig) {
        $mig->dropTables(Config::class);
    }
);
