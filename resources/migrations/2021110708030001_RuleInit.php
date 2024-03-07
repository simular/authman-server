<?php

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\Luna\Entity\Rule;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\Migration;
use Windwalker\Database\Schema\Schema;

/**
 * Migration UP: 2021110708030001RuleInit.
 *
 * @var Migration          $mig
 * @var ConsoleApplication $app
 */
$mig->up(
    static function () use ($mig) {
        $mig->createTable(
            Rule::class,
            function (Schema $schema) {
                $schema->varchar('role_id');
                $schema->varchar('name');
                $schema->varchar('type');
                $schema->varchar('action');
                $schema->varchar('target_id');
                $schema->varchar('title');
                $schema->tinyint('allow')->length(1)->nullable(true);

                $schema->addPrimaryKey(['role_id', 'name', 'target_id']);
                $schema->addIndex('role_id');
                $schema->addIndex('type');
                $schema->addIndex('name');
                $schema->addIndex('target_id');
                $schema->addIndex('action');
            }
        );
    }
);

/**
 * Migration DOWN.
 */
$mig->down(
    static function () use ($mig) {
        $mig->dropTables(Rule::class);
    }
);
