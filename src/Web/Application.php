<?php

declare(strict_types=1);

namespace App\Web;

use Windwalker\Core\Application\WebApplication;
use Windwalker\DI\Container;

class Application extends WebApplication
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
        //
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
