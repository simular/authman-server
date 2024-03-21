<?php

declare(strict_types=1);

namespace App\Service;

use Random\RandomException;
use Windwalker\Crypt\Symmetric\CipherInterface;
use Windwalker\DI\Attributes\Service;
use Windwalker\Utilities\Cache\InstanceCacheTrait;

#[Service]
class EncryptionService
{
    use InstanceCacheTrait;

    public const KEK_ITERATION_TIMES = 500000;

    public function __construct(readonly public CipherInterface $cipher)
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

    public function getTestMasterKey(): string
    {
        return $this->once(
            'test.master.key',
            function () {
                $secrets = ApiUserService::getTestSecrets();
                $salt = $secrets['salt'];
                $encSecret = $secrets['secret'];
                $encMaster = $secrets['master'];

                $kek = static::deriveKek($secrets['password'], hex2bin($salt));

                $secret = $this->cipher->decrypt($encSecret, $kek);

                return $this->cipher->decrypt($encMaster, $secret->get(false))->get(false);
            }
        );
    }
}
