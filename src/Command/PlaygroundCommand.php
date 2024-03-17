<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\ApiUserService;
use App\Service\EncryptionService;
use Brick\Math\BigInteger;
use Symfony\Component\Console\Command\Command;
use Windwalker\Console\CommandInterface;
use Windwalker\Console\CommandWrapper;
use Windwalker\Console\IOInterface;
use Windwalker\Crypt\SafeEncoder;
use Windwalker\Crypt\SecretToolkit;
use Windwalker\Crypt\Symmetric\CipherInterface;

use const Windwalker\Crypt\ENCODER_HEX;

#[CommandWrapper(
    description: ''
)]
class PlaygroundCommand implements CommandInterface
{
    public function __construct(protected EncryptionService $encryptionService, protected CipherInterface $cipher)
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

        show($secrets['secret'], SafeEncoder::decode('base64url', $secrets['secret']));

        $pass = '1234';
        $salt = BigInteger::fromBase(
            '5650da90c28fbddb2c12dd72652cb5dc',
            16
        );
        [$encSecret, $encMaster, $kek] = $this->encryptionService->createUserSecrets($pass, $salt->toBytes());

        $s2 = $this->cipher->decrypt($encSecret, $kek);
        $m2 = $this->cipher->decrypt($encMaster, SecretToolkit::decode($s2->get()));

        show($s2, $m2);

        return 0;
    }
}
