<?php

declare(strict_types=1);

namespace App\Entity;

use Windwalker\ORM\Attributes\Cast;
use Windwalker\ORM\Attributes\Column;
use Windwalker\ORM\Attributes\EntitySetup;
use Windwalker\ORM\Attributes\PK;
use Windwalker\ORM\Attributes\Table;
use Windwalker\ORM\EntityInterface;
use Windwalker\ORM\EntityTrait;
use Windwalker\ORM\Metadata\EntityMetadata;

/**
 * The Session class.
 */
#[Table('sessions', 'session')]
#[\AllowDynamicProperties]
class Session implements EntityInterface
{
    use EntityTrait;

    #[Column('id'), PK]
    protected ?string $id = null;

    #[Column('data')]
    protected string $data = '';

    #[Column('user_id')]
    protected string $userId = '';

    #[Column('remember')]
    #[Cast('bool', 'int')]
    protected bool $remember = true;

    #[Column('time')]
    protected int $time = 0;

    #[EntitySetup]
    public static function setup(EntityMetadata $metadata): void
    {
        //
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param  string|null  $id
     *
     * @return  static  Return self to support chaining.
     */
    public function setId(?string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function setData(string $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getTime(): int
    {
        return $this->time;
    }

    public function setTime(int $time): static
    {
        $this->time = $time;

        return $this;
    }

    /**
     * @return bool
     */
    public function isRemember(): bool
    {
        return $this->remember;
    }

    /**
     * @param  bool  $remember
     *
     * @return  static  Return self to support chaining.
     */
    public function setRemember(bool $remember): static
    {
        $this->remember = $remember;

        return $this;
    }
}
