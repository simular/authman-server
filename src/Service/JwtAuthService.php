<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use JetBrains\PhpStorm\ArrayShape;
use Windwalker\Core\Security\Exception\UnauthorizedException;
use Windwalker\Crypt\Hasher\PasswordHasher;
use Windwalker\Crypt\SecretToolkit;
use Windwalker\DI\Attributes\Service;
use Windwalker\ORM\ORM;
use Windwalker\Utilities\TypeCast;

use function Windwalker\chronos;

#[Service]
class JwtAuthService
{
    public function __construct(protected ORM $orm)
    {
    }

    #[ArrayShape(['string', 'string'])]
    public static function extractBasicAuth(string $credential): array
    {
        sscanf($credential, 'Basic %s', $auth);

        if (!$auth) {
            throw new \RuntimeException('Auth payload is empty', 400);
        }

        $auth = base64_decode($auth);

        $auth = explode(':', $auth, 2) + ['', ''];

        if (!isset($auth[1]) || $auth[1] === '') {
            throw new UnauthorizedException('No password', 400);
        }

        return $auth;
    }

    public function createAccessToken(User $user): string
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
            $user->getSecret(),
            'HS512'
        );
    }

    public function createRefreshToken(User $user): string
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
            $user->getSecret(),
            'HS512'
        );
    }

    public function extractToken(string $token, ?User &$user = null): array
    {
        $parts = explode('.', $token);

        if (!isset($parts[1])) {
            throw new \InvalidArgumentException('JWT format wrong.', 400);
        }

        $payload = json_decode(base64_decode($parts[1]), true);
        $data = $payload['data'];

        if (empty($data['id'])) {
            throw new \InvalidArgumentException('No user ID in JWT payload', 400);
        }

        $user = $user = $this->orm->mustFindOne(User::class, $payload['data']['id']);

        $user->setPassword('');

        $payload = JWT::decode(
            $token,
            new Key($user->getSecret(), 'HS512')
        );

        if (!$payload) {
            throw new \RuntimeException('Invalid Payload', 400);
        }

        return TypeCast::toArray($payload, true);
    }
}
