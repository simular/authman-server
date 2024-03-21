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

    public function getTestImage(): string
    {
        return $this->once(
            'test.image',
            function () {
                // $master = $this->encryptionService->getTestMasterKey();

                $icon = file_get_contents(WINDWALKER_SEEDERS . '/data/seed-icon.enc.txt');

                return $icon;
                // $iconBase64 = Base64DataUri::encode($icon, 'image/png');
                //
                // return $this->encryptionService->cipher->encrypt(
                //     $iconBase64,
                //     $master
                // );
            }
        );
    }
}
