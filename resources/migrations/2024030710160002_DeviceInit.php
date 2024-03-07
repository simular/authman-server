<?php

declare(strict_types=1);

namespace App\Migration;

use App\Entity\Device;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\Migration;
use Windwalker\Database\Schema\Schema;

/**
 * Migration UP: 2024030710160002_DeviceInit.
 *
 * @var Migration          $mig
 * @var ConsoleApplication $app
 */
$mig->up(
    static function () use ($mig) {
        $mig->createTable(
            Device::class,
            function (Schema $schema) {
                $schema->primaryUuidBinary('id');
                $schema->uuidBinary('user_id');
                $schema->varchar('title');
                $schema->varchar('type');
                $schema->varchar('device');
                $schema->varchar('os');
                $schema->varchar('ua');
                $schema->datetime('created');
                $schema->datetime('last_action_at');
                $schema->datetime('last_login');

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
        $mig->dropTables(Device::class, 'column');
    }
);
