<?php

declare(strict_types=1);

namespace App\DTO;

use Windwalker\Data\AbstractDTO;

class UserDTO extends AbstractDTO
{
    protected array $keepFields = [
        'id',
        'email',
        'name',
        'avatar',
        'lastReset',
        'params',
    ];

    protected function configure(object $data): void
    {
        //
    }
}
