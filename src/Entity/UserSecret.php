<?php

declare(strict_types=1);

namespace App\Entity;

use Ramsey\Uuid\UuidInterface;
use Windwalker\Crypt\SecretToolkit;
use Windwalker\ORM\Attributes\CastNullable;
use Windwalker\ORM\Attributes\Column;
use Windwalker\ORM\Attributes\EntitySetup;
use Windwalker\ORM\Attributes\Table;
use Windwalker\ORM\Attributes\UUIDBin;
use Windwalker\ORM\EntityInterface;
use Windwalker\ORM\EntityTrait;
use Windwalker\ORM\Metadata\EntityMetadata;

#[Table('user_secrets', 'user_secret')]
#[\AllowDynamicProperties]
class UserSecret implements EntityInterface
{
    use EntityTrait;

    #[Column('user_id')]
    #[UUIDBin]
    #[CastNullable('uuid_bin', 'uuid_bin')]
    protected UuidInterface $userId;

    #[Column('secret')]
    protected string $secret = '';

    #[Column('master')]
    protected string $master = '';

    #[Column('server_secret')]
    protected string $serverSecret = '';

    #[EntitySetup]
    public static function setup(EntityMetadata $metadata): void
    {
        //
    }

    public function getUserId(): UuidInterface
    {
        return $this->userId;
    }

    public function setUserId(UuidInterface|string $userId): static
    {
        $this->userId = UUIDBin::wrap($userId);

        return $this;
    }

    public function getSecret(): string
    {
        return $this->secret;
    }

    public function setSecret(string $secret): static
    {
        $this->secret = $secret;

        return $this;
    }

    public function getMaster(): string
    {
        return $this->master;
    }

    public function setMaster(string $master): static
    {
        $this->master = $master;

        return $this;
    }

    public function getServerSecret(): string
    {
        return $this->serverSecret;
    }

    public function setServerSecret(string $serverSecret): static
    {
        $this->serverSecret = $serverSecret;

        return $this;
    }

    public function getDecodedServerSecret(): string
    {
        return SecretToolkit::decode($this->serverSecret);
    }
}
