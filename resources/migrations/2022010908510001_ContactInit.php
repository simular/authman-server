<?php

/**
 * Part of Windwalker project.
 *
 * @copyright  Copyright (C) 2022.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Migration;

use Lyrasoft\Contact\Entity\Contact;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\Migration;
use Windwalker\Database\Schema\Schema;

/**
 * Migration UP: 2022010908510001_ContactInit.
 *
 * @var Migration          $mig
 * @var ConsoleApplication $app
 */
$mig->up(
    static function () use ($mig) {
        $mig->createTable(
            Contact::class,
            function (Schema $schema) {
                $schema->primary('id')->comment('Primary Key');
                $schema->varchar('type')->comment('Type');
                $schema->varchar('title')->comment('Title');
                $schema->varchar('email')->comment('Email');
                $schema->varchar('name')->comment('Sender Name');
                $schema->varchar('url')->length(2048)->comment('URL');
                $schema->varchar('phone')->comment('Phone');
                $schema->longtext('content')->comment('Content');
                $schema->json('details')->comment('Details JSON');
                $schema->char('state')->comment('cancel, pending, handling, done, end');
                $schema->datetime('created')->comment('Created Date');
                $schema->datetime('modified')->comment('Modified Date');
                $schema->integer('assignee_id')->comment('Assignee ID');
                $schema->integer('created_by')->comment('Author');
                $schema->integer('modified_by')->comment('Modified User');
                $schema->json('params')->comment('Params');

                $schema->addIndex('email');
                $schema->addIndex('phone');
                $schema->addIndex('name');
            }
        );
    }
);

/**
 * Migration DOWN.
 */
$mig->down(
    static function () use ($mig) {
        $mig->dropTables(Contact::class);
    }
);
