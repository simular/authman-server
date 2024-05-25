<?php

declare(strict_types=1);

namespace App\Module\Api\Traits;

use App\Entity\User;
use App\Entity\UserSecret;
use App\Enum\ErrorCode;
use Brick\Math\BigInteger;
use Brick\Math\Exception\DivisionByZeroException;
use Brick\Math\Exception\MathException;
use Brick\Math\Exception\NegativeNumberException;
use Brick\Math\Exception\NumberFormatException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Lyrasoft\Luna\Auth\SRP\SRPService;
use Windwalker\ORM\ORM;
use Windwalker\SRP\Exception\InvalidSessionProofException;

use function Windwalker\Query\uuid2bin;

trait SRPValidationTrait
{
    /**
     * @param  ORM         $orm
     * @param  SRPService  $srpService
     * @param  User        $user
     * @param  string      $A
     * @param  string      $M1
     * @param  string      $sess
     *
     * @return  array
     *
     * @throws DivisionByZeroException
     * @throws MathException
     * @throws NegativeNumberException
     * @throws NumberFormatException
     */
    protected function srpValidate(
        ORM $orm,
        SRPService $srpService,
        User $user,
        string $A,
        string $M1,
        string $sess
    ): array {
        $userSecret = $orm->mustFindOne(
            UserSecret::class,
            ['user_id' => uuid2bin($user->getId())]
        );

        if (!$loginToken = $user->getLoginToken()) {
            ErrorCode::INVALID_SESSION->throw();
        }

        $loginPayload = JWT::decode(
            $loginToken,
            new Key($userSecret->getDecodedServerSecret(), 'HS512')
        );

        $sessPayload = JWT::decode(
            $sess,
            new Key($userSecret->getDecodedServerSecret(), 'HS512')
        );

        if ($loginPayload->sess !== $sessPayload->sess) {
            ErrorCode::INVALID_SESSION->throw();
        }

        $password = $user->getPassword();

        $pf = SRPService::decodePasswordVerifier($password);

        $A = BigInteger::fromBase($A, 16);
        $M1 = BigInteger::fromBase($M1, 16);
        $b = BigInteger::fromBase($loginPayload->b, 16);
        $B = BigInteger::fromBase($loginPayload->B, 16);

        try {
            $server = $srpService->getSRPServer();
            $result = $server->step2(
                $user->getEmail(),
                $pf->salt,
                $pf->verifier,
                $A,
                $B,
                $b,
                $M1
            );

            return [$result, $loginPayload, $userSecret];
        } catch (InvalidSessionProofException) {
            ErrorCode::INVALID_CREDENTIALS->throw();
        }
    }
}
