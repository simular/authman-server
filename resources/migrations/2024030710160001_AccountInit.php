<?php

declare(strict_types=1);

namespace App\Migration;

use App\Entity\Account;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\Migration;
use Windwalker\Database\Schema\Schema;

/**
 * Migration UP: 2024030710160001_AccountInit.
 *
 * @var Migration          $mig
 * @var ConsoleApplication $app
 */
$mig->up(
    static function () use ($mig) {
        $mig->createTable(
            Account::class,
            function (Schema $schema) {
                $schema->primaryUuidBinary('id');
                $schema->uuidBinary('user_id');
                $schema->varchar('title');
                $schema->varchar('secret');
                $schema->varchar('url')->length(512);
                $schema->varchar('icon');
                $schema->datetime('created');
                $schema->datetime('modified');
                $schema->json('params');

                $schema->addIndex('user_id');
            }
        );
    }
);

/**
 * Migration DOWN.
 */
$mig->down(
    static function () use ($mig) {
        $mig->dropTables(Account::class, 'column');
    }
);
