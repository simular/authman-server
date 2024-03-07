<?php

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\Luna\Entity\Tag;
use Lyrasoft\Luna\Entity\TagMap;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\Migration;
use Windwalker\Database\Schema\Schema;

/**
 * Migration UP: 2021112206280001_TagInit.
 *
 * @var Migration          $mig
 * @var ConsoleApplication $app
 */
$mig->up(
    static function () use ($mig) {
        $mig->createTable(
            Tag::class,
            function (Schema $schema) {
                $schema->primary('id')->comment('Primary Key');
                $schema->varchar('title')->comment('Title');
                $schema->varchar('alias')->comment('Alias');
                $schema->bool('state')->comment('0: unpublished, 1:published');
                $schema->datetime('created')->comment('Created Date');
                $schema->datetime('modified')->comment('Modified Date');
                $schema->integer('created_by')->comment('Author');
                $schema->integer('modified_by')->comment('Modified User');
                $schema->char('language')->length(7)->comment('Language');
                $schema->json('params')->comment('Params');

                $schema->addIndex('alias');
                $schema->addIndex('language');
                $schema->addIndex('created_by');
            }
        );
        $mig->createTable(
            TagMap::class,
            function (Schema $schema) {
                $schema->integer('tag_id')->comment('Tag ID');
                $schema->integer('target_id')->comment('Target ID');
                $schema->varchar('type')->comment('Type');

                $schema->addIndex('tag_id');
                $schema->addIndex('target_id');
                $schema->addIndex('type');
            }
        );
    }
);

/**
 * Migration DOWN.
 */
$mig->down(
    static function () use ($mig) {
        $mig->dropTables(Tag::class, TagMap::class);
    }
);
