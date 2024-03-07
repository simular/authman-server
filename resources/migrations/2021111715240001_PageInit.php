<?php

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\Luna\Entity\Page;
use Lyrasoft\Luna\Entity\PageTemplate;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\Migration;
use Windwalker\Database\Schema\Schema;

/**
 * Migration UP: 2021111715240001_PageInit.
 *
 * @var Migration          $mig
 * @var ConsoleApplication $app
 */
$mig->up(
    static function () use ($mig) {
        $mig->createTable(
            Page::class,
            function (Schema $schema) {
                $schema->primary('id')->comment('Primary Key');
                $schema->integer('category_id')->comment('Category');
                $schema->varchar('extends')->comment('Extends Layout');
                $schema->varchar('title')->comment('Title');
                $schema->varchar('alias')->comment('Alias');
                $schema->varchar('image')->comment('Image');
                $schema->json('content')->comment('Page data');
                $schema->longtext('css')->comment('CSS');
                $schema->json('meta')->comment('Metadata');
                $schema->bool('state')->comment('0: unpublished, 1:published');
                $schema->integer('ordering')->comment('Ordering');
                $schema->datetime('created')->comment('Created Date');
                $schema->integer('created_by')->comment('Author');
                $schema->datetime('modified')->comment('Modified Date');
                $schema->integer('modified_by')->comment('Modified User');
                $schema->char('language')->length(7)->comment('Language');
                $schema->json('params')->comment('Params');

                $schema->addIndex('category_id');
                $schema->addIndex('extends');
                $schema->addIndex('alias');
                $schema->addIndex('language');
            }
        );

        $mig->createTable(
            PageTemplate::class,
            function (Schema $schema) {
                $schema->primary('id')->comment('Primary Key');
                $schema->varchar('title')->comment('Title');
                $schema->char('type')->length(6);
                $schema->text('description');
                $schema->varchar('image')->comment('Image');
                $schema->json('content')->comment('Page data');
                $schema->datetime('created')->comment('Created Date');
                $schema->integer('created_by')->comment('Author');
                $schema->datetime('modified')->comment('Modified Date');
                $schema->integer('modified_by')->comment('Modified User');
                $schema->json('params')->comment('Params');
            }
        );
    }
);

/**
 * Migration DOWN.
 */
$mig->down(
    static function () use ($mig) {
        $mig->dropTables(Page::class, PageTemplate::class);
    }
);
