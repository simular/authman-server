<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Entity\UserSecret;
use Random\RandomException;
use Windwalker\Crypt\SecretToolkit;
use Windwalker\Crypt\Symmetric\CipherInterface;
use Windwalker\DI\Attributes\Service;

use const Windwalker\Crypt\ENCODER_HEX;

#[Service]
class EncryptionService
{
    public const KEK_ITERATION_TIMES = 500000;

    public function __construct(protected CipherInterface $cipher)
    {
    }

    /**
     * @param  string  $password
     * @param  string  $salt
     *
     * @return  array<string>
     *
     * @throws RandomException
     *
     * @internal  Currently no use in server
     */
    public function createUserSecrets(string $password, string $salt): array
    {
        $secretKey = random_bytes(16);
        $masterKey = random_bytes(32);
        $kek = static::deriveKek($password, $salt);

        $encSecret = $this->cipher->encrypt($secretKey, $kek);
        $encMaster = $this->cipher->encrypt($masterKey, $secretKey);

        return [$encSecret, $encMaster, $kek];
    }

    public static function deriveKek(string $password, string $salt): string
    {
        return hash_pbkdf2(
            'SHA256',
            $password,
            $salt,
            static::KEK_ITERATION_TIMES,
            32,
            true
        );
    }
}
