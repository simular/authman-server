<?php

declare(strict_types=1);

namespace App\Service;

use Unicorn\Flysystem\Base64DataUri;
use Windwalker\DI\Attributes\Service;
use Windwalker\Utilities\Cache\InstanceCacheTrait;

#[Service]
class AccountService
{
    use InstanceCacheTrait;

    public function __construct(protected EncryptionService $encryptionService)
    {
    }
}
