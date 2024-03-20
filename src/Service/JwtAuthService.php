<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Entity\UserSecret;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use JetBrains\PhpStorm\ArrayShape;
use Windwalker\Core\DateTime\Chronos;
use Windwalker\Core\Security\Exception\UnauthorizedException;
use Windwalker\Crypt\Hasher\PasswordHasher;
use Windwalker\Crypt\SecretToolkit;
use Windwalker\DI\Attributes\Service;
use Windwalker\ORM\ORM;
use Windwalker\Utilities\TypeCast;

use function Windwalker\chronos;
use function Windwalker\Query\uuid2bin;

#[Service]
class JwtAuthService
{
    public function __construct(protected ORM $orm)
    {
    }

    public function createAccessToken(User $user, UserSecret $userSecret): string
    {
        $now = chronos();

        $data = [
            'iat' => (int) $now->toUnix(),
            'jti' => PasswordHasher::genRandomPassword(32),
            'iss' => 'Simular',
            'nbf' => (int) $now->toUnix(),
            'exp' => $now->modify('+3months')->toUnix(),
            'email' => $user->getEmail(),
            'id' => $user->getId(),
            'type' => 'access'
        ];

        return JWT::encode(
            $data,
            $userSecret->getDecodedServerSecret(),
            'HS512'
        );
    }

    public function createRefreshToken(User $user, UserSecret $userSecret): string
    {
        $now = chronos();

        $data = [
            'iat' => (int) $now->toUnix(),
            'jti' => PasswordHasher::genRandomPassword(32),
            'iss' => 'Simular',
            'nbf' => (int) $now->toUnix(),
            'exp' => $now->modify('+6month')->toUnix(),
            'email' => $user->getEmail(),
            'id' => $user->getId(),
            'type' => 'refresh'
        ];

        return JWT::encode(
            $data,
            $userSecret->getDecodedServerSecret(),
            'HS512'
        );
    }

    public function extractAccessTokenFromHeader(string $authorization, ?User &$user = null): array
    {
        sscanf($authorization, 'Bearer %s', $token);

        if (!$token) {
            return [];
        }

        return $this->extractAccessToken((string) $token, $user);
    }

    public function extractAccessToken(string $token, ?User &$user = null): array
    {
        $parts = explode('.', $token);

        if (!isset($parts[1])) {
            throw new \InvalidArgumentException('JWT format wrong.', 400);
        }

        $payload = json_decode(base64_decode($parts[1]), true, 512, JSON_THROW_ON_ERROR);

        if (empty($payload['id'])) {
            throw new \InvalidArgumentException('No user ID in JWT payload', 400);
        }

        $user = $user = $this->orm->mustFindOne(User::class, ['id' => uuid2bin($payload['id'])]);

        $userSecret = $user->getSecretEntity();

        $payload = JWT::decode(
            $token,
            new Key($userSecret->getDecodedServerSecret(), 'HS512')
        );

        if (!$payload) {
            throw new \RuntimeException('Invalid Payload', 400);
        }

        $issuedAt = Chronos::createFromFormat('U', (string) $payload->iat);

        if ($issuedAt < $user->getSessValidForm()) {
            $user = null;

            $ex = new ExpiredException('User token expired');
            $ex->setPayload($payload);
            throw $ex;
        }

        return TypeCast::toArray($payload, true);
    }
}
