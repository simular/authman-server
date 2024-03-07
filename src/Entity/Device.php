<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\DeviceType;
use Ramsey\Uuid\UuidInterface;
use Windwalker\Core\DateTime\Chronos;
use Windwalker\Core\DateTime\ServerTimeCast;
use Windwalker\ORM\Attributes\Cast;
use Windwalker\ORM\Attributes\CastNullable;
use Windwalker\ORM\Attributes\Column;
use Windwalker\ORM\Attributes\CreatedTime;
use Windwalker\ORM\Attributes\EntitySetup;
use Windwalker\ORM\Attributes\PK;
use Windwalker\ORM\Attributes\Table;
use Windwalker\ORM\Attributes\UUIDBin;
use Windwalker\ORM\EntityInterface;
use Windwalker\ORM\EntityTrait;
use Windwalker\ORM\Metadata\EntityMetadata;

#[Table('devices', 'device')]
#[\AllowDynamicProperties]
class Device implements EntityInterface
{
    use EntityTrait;

    #[Column('id'), PK]
    #[UUIDBin]
    #[CastNullable('uuid_bin', 'uuid_bin')]
    protected ?UuidInterface $id = null;

    #[Column('user_id')]
    #[UUIDBin]
    #[CastNullable('uuid_bin', 'uuid_bin')]
    protected UuidInterface $userId;

    #[Column('title')]
    protected string $title = '';

    #[Column('type')]
    #[Cast(DeviceType::class)]
    protected DeviceType $type;

    #[Column('device')]
    protected string $device = '';

    #[Column('os')]
    protected string $os = '';

    #[Column('ua')]
    protected string $ua = '';

    #[Column('created')]
    #[CastNullable(ServerTimeCast::class)]
    #[CreatedTime]
    protected ?Chronos $created = null;

    #[Column('last_action_at')]
    #[CastNullable(ServerTimeCast::class)]
    protected ?Chronos $lastActionAt = null;

    #[Column('last_login')]
    #[CastNullable(ServerTimeCast::class)]
    protected ?Chronos $lastLogin = null;

    #[EntitySetup]
    public static function setup(EntityMetadata $metadata): void
    {
        //
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function setId(UuidInterface|string|null $id): static
    {
        $this->id = UUIDBin::tryWrap($id);

        return $this;
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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDevice(): string
    {
        return $this->device;
    }

    public function setDevice(string $device): static
    {
        $this->device = $device;

        return $this;
    }

    public function getCreated(): ?Chronos
    {
        return $this->created;
    }

    public function setCreated(\DateTimeInterface|string|null $created): static
    {
        $this->created = Chronos::wrapOrNull($created);

        return $this;
    }

    public function getLastActionAt(): ?Chronos
    {
        return $this->lastActionAt;
    }

    public function setLastActionAt(\DateTimeInterface|string|null $lastActionAt): static
    {
        $this->lastActionAt = Chronos::wrapOrNull($lastActionAt);

        return $this;
    }

    public function getLastLogin(): ?Chronos
    {
        return $this->lastLogin;
    }

    public function setLastLogin(\DateTimeInterface|string|null $lastLogin): static
    {
        $this->lastLogin = Chronos::wrapOrNull($lastLogin);

        return $this;
    }

    public function getOs(): string
    {
        return $this->os;
    }

    public function setOs(string $os): static
    {
        $this->os = $os;

        return $this;
    }

    public function getUa(): string
    {
        return $this->ua;
    }

    public function setUa(string $ua): static
    {
        $this->ua = $ua;

        return $this;
    }

    public function getType(): DeviceType
    {
        return $this->type;
    }

    public function setType(string|DeviceType $type): static
    {
        $this->type = DeviceType::wrap($type);

        return $this;
    }
}
