<?php

declare(strict_types=1);

namespace App\Service;

use App\Data\ApiTokenPayload;
use App\Entity\User;
use App\Entity\UserSecret;
use App\Enum\ErrorCode;
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
            'iss' => static::getIssuer(),
            'nbf' => (int) $now->toUnix(),
            'exp' => $now->modify('+7days')->toUnix(),
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
            'iss' => static::getIssuer(),
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

    public function extractAccessTokenFromHeader(string $authorization, ?User &$user = null): ApiTokenPayload
    {
        sscanf($authorization, 'Bearer %s', $token);

        if (!$token) {
            throw new \RuntimeException('Token is empty.', 400);
        }

        return $this->extractAccessToken((string) $token, $user);
    }

    public function extractAccessToken(string $token, ?User &$user = null): ApiTokenPayload
    {
        $parts = explode('.', $token);

        if (!isset($parts[1])) {
            throw new \RuntimeException('JWT format wrong.', 400);
        }

        $payload = json_decode(base64_decode($parts[1]), true, 512, JSON_THROW_ON_ERROR);

        if (empty($payload['id'])) {
            throw new \RuntimeException('No user ID in JWT payload', 400);
        }

        if ($payload['iss'] !== static::getIssuer()) {
            throw new \RuntimeException('Invalid token', 400);
        }

        $user = $user = $this->orm->mustFindOne(User::class, ['id' => uuid2bin($payload['id'])]);

        $userSecret = $user->getSecretEntity();

        try {
            $payload = JWT::decode(
                $token,
                new Key($userSecret->getDecodedServerSecret(), 'HS512')
            );
        } catch (ExpiredException $e) {
            ErrorCode::ACCESS_TOKEN_EXPIRED->throw();
        }

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

        return ApiTokenPayload::wrap(
            TypeCast::toArray($payload, true)
        );
    }

    public static function getIssuer(): string
    {
        return env('JWT_ISSUER') ?: 'Authman';
    }
}
