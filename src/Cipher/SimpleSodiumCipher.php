<?php

declare(strict_types=1);

namespace App\Cipher;

use Windwalker\Crypt\CryptHelper;
use Windwalker\Crypt\HiddenString;
use Windwalker\Crypt\Key;
use Windwalker\Crypt\SafeEncoder;
use Windwalker\Crypt\Symmetric\CipherInterface;

use const Windwalker\Crypt\ENCODER_BASE64URLSAFE;

class SimpleSodiumCipher implements CipherInterface
{
    public const SALT_SIZE = 16;

    public const PBKDF_ITERATION_TIMES = 500000;

    public function decrypt(
        string $str,
        #[\SensitiveParameter] Key|string $key,
        callable|string $encoder = ENCODER_BASE64URLSAFE
    ): HiddenString {
        $message = SafeEncoder::decode($encoder, $str);

        // Split string
        $nonce = CryptHelper::substr($message, 0, SODIUM_CRYPTO_BOX_NONCEBYTES);
        $salt = CryptHelper::substr(
            $message,
            SODIUM_CRYPTO_BOX_NONCEBYTES,
            static::SALT_SIZE
        );
        $encrypted = CryptHelper::substr(
            $message,
            SODIUM_CRYPTO_BOX_NONCEBYTES + static::SALT_SIZE
        );

        $encKey = static::derivativeEncKey($key, $salt);

        sodium_memzero($message);

        $plaintext = sodium_crypto_secretbox_open(
            $encrypted,
            $nonce,
            $encKey
        );

        sodium_memzero($encrypted);
        sodium_memzero($nonce);

        return new HiddenString($plaintext);
    }

    public function encrypt(
        #[\SensitiveParameter] HiddenString|string $str,
        #[\SensitiveParameter] Key|string $key,
        callable|string $encoder = ENCODER_BASE64URLSAFE
    ): string {
        $str = HiddenString::strip($str);

        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $salt = random_bytes(self::SALT_SIZE);

        $encKey = static::derivativeEncKey($key, $salt);

        $encrypted = sodium_crypto_secretbox(
            $str,
            $nonce,
            $encKey
        );

        $message = $salt . $nonce . $encrypted;

        // Wipe every superfluous piece of data from memory
        sodium_memzero($encKey);
        sodium_memzero($nonce);
        sodium_memzero($salt);
        sodium_memzero($encrypted);

        return SafeEncoder::encode($encoder, $message);
    }

    public static function generateKey(?int $length = null): Key
    {
        return new Key(random_bytes($length));
    }

    /**
     * @param  Key|string  $key
     * @param  string      $salt
     *
     * @return  string
     */
    public static function derivativeEncKey(Key|string $key, string $salt): string
    {
        return hash_pbkdf2(
            'SHA256',
            Key::strip($key),
            $salt,
            static::PBKDF_ITERATION_TIMES,
            32,
            true
        );
    }
}
