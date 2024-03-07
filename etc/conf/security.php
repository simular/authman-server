<?php

declare(strict_types=1);

use Windwalker\Core\Manager\CryptoManager;
use Windwalker\Core\Manager\HasherManager;
use Windwalker\Crypt\Hasher\Hasher;
use Windwalker\Crypt\Hasher\HasherInterface;
use Windwalker\Crypt\Hasher\PasswordHasher;
use Windwalker\Crypt\Hasher\PasswordHasherInterface;
use Windwalker\Crypt\Symmetric\CipherInterface;
use Windwalker\Crypt\Symmetric\OpensslCipher;
use Windwalker\Crypt\Symmetric\SodiumCipher;
use Windwalker\DI\Container;

use function Windwalker\DI\create;

return [
    'crypto' => [
        'default' => 'sodium',

        'factories' => [
            'instances' => [
                'sodium' => create(SodiumCipher::class),
                'blowfish' => create(OpensslCipher::class, 'blowfish'),
            ],
        ],
    ],

    'hasher' => [
        'default' => 'hash',

        'factories' => [
            'instances' => [
                'hash' => create(Hasher::class, 'sha256'),
                'password' => create(PasswordHasher::class, PASSWORD_DEFAULT),
            ],
        ],
    ],

    'bindings' => [
        CryptoManager::class,
        HasherManager::class,
        CipherInterface::class => static function (Container $container) {
            return $container->get(CryptoManager::class)->get();
        },
        HasherInterface::class => static function (Container $container) {
            return $container->get(HasherManager::class)->get();
        },
        PasswordHasherInterface::class => static function (Container $container) {
            return $container->get(HasherManager::class)->get('password');
        },
    ],
];
