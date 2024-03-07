<?php

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\Luna\Entity\Article;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\Migration;
use Windwalker\Database\Schema\Schema;

/**
 * Migration UP: 2021110708050001_ArticleInit.
 *
 * @var Migration $mig
 * @var ConsoleApplication $app
 */
$mig->up(
    static function () use ($mig) {
        $mig->createTable(
            Article::class,
            function (Schema $schema) {
                $schema->primary('id')->comment('Primary Key');
                $schema->integer('category_id')->comment('Category ID');
                $schema->integer('page_id')->comment('Page ID');
                $schema->char('type')->length(15)->comment('Content Type');
                $schema->varchar('title')->comment('Title');
                $schema->varchar('alias')->comment('Alias');
                $schema->varchar('image')->comment('Main Image');
                $schema->longtext('introtext')->comment('Intro Text');
                $schema->longtext('fulltext')->comment('Full Text');
                $schema->tinyint('state')->length(1)->comment('0: unpublished, 1:published');
                $schema->integer('ordering')->comment('Ordering');
                $schema->json('extra')->comment('Extra Data');
                $schema->datetime('created')->comment('Created Date');
                $schema->datetime('modified')->comment('Modified Date');
                $schema->integer('created_by')->comment('Author');
                $schema->integer('modified_by')->comment('Modified User');
                $schema->char('language')->length(7)->comment('Language');
                $schema->json('params')->comment('Params');

                $schema->addIndex('category_id');
                $schema->addIndex('page_id');
                $schema->addIndex('alias');
                $schema->addIndex('language');
                $schema->addIndex('created_by');
            }
        );
    }
);

/**
 * Migration DOWN.
 */
$mig->down(
    static function () use ($mig) {
        $mig->dropTables(Article::class);
    }
);
