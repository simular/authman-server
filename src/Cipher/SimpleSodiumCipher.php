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
    public const NONCE_SIZE = SODIUM_CRYPTO_BOX_NONCEBYTES;
    public const SALT_SIZE = 16;
    public const HKDF_SIZE = 32;
    public const HMAC_SIZE = 64;

    public const PBKDF_ITERATION_TIMES = 500000;

    public function decrypt(
        string $str,
        #[\SensitiveParameter] Key|string $key,
        callable|string $encoder = ENCODER_BASE64URLSAFE
    ): HiddenString {
        $message = SafeEncoder::decode($encoder, $str);

        $length = CryptHelper::strlen($message);

        // Split string
        $nonce = CryptHelper::substr($message, 0, static::NONCE_SIZE);
        $salt = CryptHelper::substr(
            $message,
            static::NONCE_SIZE,
            static::SALT_SIZE
        );
        $encrypted = CryptHelper::substr(
            $message,
            static::NONCE_SIZE + static::SALT_SIZE,
            $length - (static::NONCE_SIZE + static::SALT_SIZE + static::HMAC_SIZE)
        );
        $hmac = CryptHelper::substr(
            $message,
            $length - static::HMAC_SIZE
        );

        $encKey = static::deriveHkdf($key, 'Enc', $salt);
        $hmacKey = static::deriveHkdf($key, 'Auth', $salt);

        sodium_memzero($message);

        $calc = sodium_crypto_generichash($nonce . $salt . $encrypted, $hmacKey, static::HMAC_SIZE);

        if (!hash_equals($hmac, $calc)) {
            throw new \UnexpectedValueException('Invalid message authentication code');
        }

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

        $encKey = static::deriveHkdf($key, 'Enc', $salt);
        $hmacKey = static::deriveHkdf($key, 'Auth', $salt);

        $encrypted = sodium_crypto_secretbox(
            $str,
            $nonce,
            $encKey
        );

        $hmac = sodium_crypto_generichash($nonce . $salt . $encrypted, $hmacKey, static::HMAC_SIZE);

        $message = $nonce . $salt . $encrypted . $hmac;

        // Wipe every superfluous piece of data from memory
        sodium_memzero($encKey);
        sodium_memzero($hmacKey);
        sodium_memzero($nonce);
        sodium_memzero($salt);
        sodium_memzero($encrypted);
        sodium_memzero($hmac);

        return SafeEncoder::encode($encoder, $message);
    }

    public static function generateKey(?int $length = null): Key
    {
        return new Key(random_bytes($length));
    }

    /**
     * @param  Key|string  $key
     * @param  string      $salt
     * @param  int         $iteration
     * @param  int         $length
     * @param  bool        $binary
     *
     * @return  string
     */
    public static function derivePbkdf2(
        #[\SensitiveParameter] Key|string $key,
        #[\SensitiveParameter] string $salt,
        int $iteration = 100000,
        int $length = 32,
        bool $binary = true
    ): string {
        return hash_pbkdf2(
            'SHA256',
            Key::strip($key),
            $salt,
            $iteration,
            $length,
            $binary
        );
    }

    /**
     * @param  Key|string  $key
     * @param  string      $info
     * @param  string      $salt
     *
     * @return  string
     */
    public static function deriveHkdf(
        #[\SensitiveParameter] Key|string $key,
        string $info,
        string $salt
    ): string {
        return hash_hkdf(
            'SHA256',
            Key::strip($key),
            static::HKDF_SIZE,
            $info,
            $salt
        );
    }
}
