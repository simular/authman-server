<?php

declare(strict_types=1);

namespace App\Entity;

use Windwalker\ORM\Attributes\AutoIncrement;
use Windwalker\ORM\Attributes\CastNullable;
use Windwalker\ORM\Attributes\Column;
use Windwalker\ORM\Attributes\EntitySetup;
use Windwalker\ORM\Attributes\PK;
use Windwalker\ORM\Attributes\Table;
use Windwalker\ORM\EntityInterface;
use Windwalker\ORM\EntityTrait;
use Windwalker\ORM\Metadata\EntityMetadata;

/**
 * The Access class.
 */
#[Table('rules', 'rule')]
#[\AllowDynamicProperties]
class Rule implements EntityInterface
{
    use EntityTrait;

    #[Column('id'), PK, AutoIncrement]
    protected ?int $id = null;

    #[Column('role_id'), PK]
    protected string|int $roleId = '';

    #[Column('name'), PK]
    protected string $name = '';

    #[Column('type')]
    protected string $type = '';

    #[Column('action')]
    protected string $action = '';

    #[Column('target_id'), PK]
    protected string $targetId = '';

    #[Column('title')]
    protected string $title = '';

    #[Column('allow')]
    #[CastNullable('bool')]
    protected ?bool $allow = null;

    #[EntitySetup]
    public static function setup(EntityMetadata $metadata): void
    {
        //
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param  int|null  $id
     *
     * @return  static  Return self to support chaining.
     */
    public function setId(?int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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

    public function getAction(): string
    {
        return $this->action;
    }

    public function setAction(string $action): static
    {
        $this->action = $action;

        return $this;
    }

    public function isAllow(): bool
    {
        return (bool) $this->allow;
    }

    public function getAllow(): ?bool
    {
        return $this->allow;
    }

    public function setAllow(bool|int|null $allow): static
    {
        if (is_int($allow)) {
            $allow = (bool) $allow;
        }

        $this->allow = $allow;

        return $this;
    }

    public function isInherited(): bool
    {
        return $this->allow === null;
    }

    public function getTargetId(): string
    {
        return $this->targetId;
    }

    public function setTargetId(string|int $targetId): static
    {
        $this->targetId = (string) $targetId;

        return $this;
    }

    /**
     * @return int|string
     */
    public function getRoleId(): int|string
    {
        return $this->roleId;
    }

    /**
     * @param  int|string  $roleId
     *
     * @return  static  Return self to support chaining.
     */
    public function setRoleId(int|string $roleId): static
    {
        $this->roleId = $roleId;

        return $this;
    }

    public function isStatic(): bool
    {
        return !is_numeric($this->roleId);
    }
}
