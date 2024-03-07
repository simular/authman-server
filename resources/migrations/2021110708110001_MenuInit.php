<?php

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\Luna\Entity\Menu;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\Migration;
use Windwalker\Database\Schema\Schema;
use Windwalker\ORM\NestedSetMapper;
use Windwalker\ORM\ORM;

/**
 * Migration UP: 2021110708110001_MenuInit.
 *
 * @var Migration          $mig
 * @var ConsoleApplication $app
 */
$mig->up(
    static function (ORM $orm) use ($mig) {
        $mig->createTable(
            Menu::class,
            function (Schema $schema) {
                $schema->primary('id')->comment('Primary Key');
                $schema->integer('parent_id')->comment('Parent ID');
                $schema->integer('lft')->comment('Left Key');
                $schema->integer('rgt')->comment('Right key');
                $schema->integer('level')->comment('Nested Level');
                $schema->varchar('path')->length(1024)->comment('Alias Path');
                $schema->varchar('type')->length(50)->comment('Content Type');
                $schema->varchar('view')->comment('View Name');
                $schema->varchar('title')->comment('Title');
                $schema->varchar('alias')->comment('Alias');
                $schema->varchar('url')->comment('URL');
                $schema->char('target')->length(10)->comment('Target');
                $schema->json('variables')->comment('Vars');
                $schema->varchar('image')->comment('Main Image');
                $schema->tinyint('state')->length(1)->comment('0: unpublished, 1:published');
                $schema->bool('hidden');
                $schema->datetime('created')->comment('Created Date');
                $schema->datetime('modified')->comment('Modified Date');
                $schema->integer('created_by')->comment('Author');
                $schema->integer('modified_by')->comment('Modified User');
                $schema->char('language')->length(7)->comment('Language');
                $schema->json('params')->comment('Params');

                $schema->addIndex('alias');
                $schema->addIndex('path');
                $schema->addIndex('view');
                $schema->addIndex('type');
                $schema->addIndex(['lft', 'rgt']);
                $schema->addIndex('language');
                $schema->addIndex('created_by');
            }
        );

        /** @var NestedSetMapper $mapper */
        $mapper = $orm->mapper(Menu::class);

        if (!$mapper->getRoot()) {
            $mapper->createRoot();
        }
    }
);

/**
 * Migration DOWN.
 */
$mig->down(
    static function () use ($mig) {
        $mig->dropTables(Menu::class);
    }
);
