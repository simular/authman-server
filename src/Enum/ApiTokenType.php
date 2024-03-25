<?php

declare(strict_types=1);

namespace App\Enum;

use Windwalker\Utilities\Enum\EnumTranslatableInterface;
use Windwalker\Utilities\Enum\EnumTranslatableTrait;
use Windwalker\Utilities\Contract\LanguageInterface;

enum ApiTokenType: string implements EnumTranslatableInterface
{
    use EnumTranslatableTrait;

    case ACCESS = 'access';
    case REFRESH = 'refresh';

    public function trans(LanguageInterface $lang, ...$args): string
    {
        return $lang->trans('app.access.token.type.' . $this->getKey());
    }
}
