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
use Windwalker\Crypt\SecretToolkit;
use Windwalker\Crypt\Symmetric\CipherInterface;

use const Windwalker\Crypt\ENCODER_HEX;

#[CommandWrapper(
    description: ''
)]
class TestSecretsCommand implements CommandInterface
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
        // $secrets = ApiUserService::getTestSecrets();
        //
        // show($secrets['secret'], SafeEncoder::decode('base64url', $secrets['secret']));

        $pass = '1234';
        $salt = random_bytes(16);

        [$encSecret, $encMaster, $kek] = $this->encryptionService->createUserSecrets(
            $pass,
            $salt
        );

        $s2 = $this->cipher->decrypt($encSecret, $kek);
        $m2 = $this->cipher->decrypt($encMaster, $s2->get(false));

        $json = json_encode(
            [
                'password' => $pass,
                'salt' => bin2hex($salt),
                'kek' => SecretToolkit::encode($kek, ENCODER_HEX),
                'secret_hex' => bin2hex($s2->get(false)),
                'secret' => $encSecret,
                'master_hex' => bin2hex($m2->get(false)),
                'master' => $encMaster,
            ],
            JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT
        );

        file_put_contents(ApiUserService::getTestSecretsFilePath(), $json);

        $io->writeln('Write test secrets to: ' . ApiUserService::getTestSecretsFilePath());

        return 0;
    }
}
