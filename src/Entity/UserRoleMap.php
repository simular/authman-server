<?php

declare(strict_types=1);

namespace App\Entity;

use Ramsey\Uuid\UuidInterface;
use Windwalker\ORM\Attributes\CastNullable;
use Windwalker\ORM\Attributes\Column;
use Windwalker\ORM\Attributes\EntitySetup;
use Windwalker\ORM\Attributes\PK;
use Windwalker\ORM\Attributes\Table;
use Windwalker\ORM\Attributes\UUIDBin;
use Windwalker\ORM\EntityInterface;
use Windwalker\ORM\EntityTrait;
use Windwalker\ORM\Metadata\EntityMetadata;

/**
 * The UserRoleMap class.
 */
#[Table('user_role_maps', 'user_role_map')]
#[\AllowDynamicProperties]
class UserRoleMap implements EntityInterface
{
    use EntityTrait;

    #[Column('user_id'), PK]
    #[UUIDBin]
    #[CastNullable('uuid_bin', 'uuid_bin')]
    protected UuidInterface $userId;

    #[Column('role_id'), PK]
    protected string|int $roleId = '';

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

    public function getRoleId(): string|int
    {
        return $this->roleId;
    }

    public function setRoleId(string|int $roleId): static
    {
        $this->roleId = $roleId;

        return $this;
    }

    public function isStatic(): bool
    {
        return !is_numeric($this->roleId);
    }
}
