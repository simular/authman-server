<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\ApiUserService;
use Symfony\Component\Console\Command\Command;
use Windwalker\Console\CommandInterface;
use Windwalker\Console\CommandWrapper;
use Windwalker\Console\IOInterface;

#[CommandWrapper(
    description: ''
)]
class PlaygroundCommand implements CommandInterface
{
    public function __construct()
    {
    }

    /**
     * configure
     *
     * @param  Command  $command
     *
     * @return  void
     */
    public function configure(Command $command): void
    {
        //
    }

    /**
     * Executes the current command.
     *
     * @param  IOInterface  $io
     *
     * @return  int Return 0 is success, 1-255 is failure.
     */
    public function execute(IOInterface $io): int
    {
        $secrets = ApiUserService::getTestSecrets();

        $kek = sodium_crypto_kdf_derive_from_key(
            32,
            1,
            '#__kek__',
            random_bytes(32)
        );
        show($kek);
        exit(' @Checkpoint');

        // $key = random_bytes(32);
        //
        // $enc = sodium_crypto_secretbox(
        //     '1234',
        //     $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES),
        //     $key
        // );
        // $authKey = sodium_crypto_auth_keygen();
        // $auth1 = sodium_crypto_auth('1234', $authKey);
        // $auth2 = sodium_crypto_auth($enc, $authKey);
        //
        // show($auth1, $auth2);
        //
        // // show($enc);
        // // $enc = substr($enc, 0, -1);
        // // show($enc);
        //
        // $m = sodium_crypto_secretbox_open(
        //     $enc,
        //     $nonce,
        //     $key
        // );
        // show($m);
        //
        $kek = sodium_crypto_pwhash(
            32,
            '1234',
            random_bytes(16),
            SODIUM_CRYPTO_PWHASH_OPSLIMIT_MODERATE,
            SODIUM_CRYPTO_PWHASH_MEMLIMIT_MODERATE,
        );
        //
        show($kek);

        return 0;
    }
}
