<?php

declare(strict_types=1);

namespace App\Schedule;

use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Schedule\Schedule;

/**
 * @var Schedule           $schedule
 * @var ConsoleApplication $app
 */

$schedule->daily('daily_backup')
    ->handler(
        function (ConsoleApplication $app) {
            $dir = env('SQL_BACKUP_DIR') ?: WINDWALKER_TEMP . '/backups';
            $dir .= '/daily';
            $app->runProcess('php windwalker db:export --dir "' . $dir . '" -z');
        }
    );

$schedule->task('weekly_backup')
    ->dayOfWeek(0)
    ->handler(
        function (ConsoleApplication $app) {
            $dir = env('SQL_BACKUP_DIR') ?: WINDWALKER_TEMP . '/backups';
            $dir .= '/weekly';
            $app->runProcess('php windwalker db:export --dir "' . $dir . '" -z');
        }
    );
