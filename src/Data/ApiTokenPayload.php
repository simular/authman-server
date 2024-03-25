<?php

declare(strict_types=1);

namespace App\Data;

use App\Enum\ApiTokenType;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Windwalker\Data\ValueObject;

class ApiTokenPayload extends ValueObject
{
    public int $iat = 0;

    public string $iss = '';

    public int $nbf = 0;

    public int $exp = 0;

    public string $email = '';

    public UuidInterface $id;

    public ApiTokenType $type;

    public function getIat(): int
    {
        return $this->iat;
    }

    public function setIat(int $iat): static
    {
        $this->iat = $iat;

        return $this;
    }

    public function getIss(): string
    {
        return $this->iss;
    }

    public function setIss(string $iss): static
    {
        $this->iss = $iss;

        return $this;
    }

    public function getNbf(): int
    {
        return $this->nbf;
    }

    public function setNbf(int $nbf): static
    {
        $this->nbf = $nbf;

        return $this;
    }

    public function getExp(): int
    {
        return $this->exp;
    }

    public function setExp(int $exp): static
    {
        $this->exp = $exp;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function setId(UuidInterface|string $id): static
    {
        $this->id = Uuid::fromString($id);

        return $this;
    }

    public function getType(): ApiTokenType
    {
        return $this->type;
    }

    public function setType(ApiTokenType|string $type): static
    {
        $this->type = ApiTokenType::wrap($type);

        return $this;
    }
}
