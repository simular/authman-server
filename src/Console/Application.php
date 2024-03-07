<?php

declare(strict_types=1);

namespace App\Console;

use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\DI\Container;

class Application extends ConsoleApplication
{
    /**
     * Your booting logic.
     *
     * @param  Container  $container
     *
     * @return  void
     */
    protected function booting(Container $container): void
    {
        // $this->prepareWebSimulator();
    }

    /**
     * Your Terminating logic.
     *
     * @param  Container  $container
     *
     * @return  void
     */
    protected function terminating(Container $container): void
    {
        //
    }
}
