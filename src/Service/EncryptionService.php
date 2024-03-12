<?php

declare(strict_types=1);

namespace App\Service;

use Windwalker\Crypt\Symmetric\CipherInterface;
use Windwalker\DI\Attributes\Service;

#[Service]
class EncryptionService
{
    public function __construct(protected CipherInterface $cipher)
    {
    }
}
