<?php

declare(strict_types=1);

namespace App\Enum;

use Windwalker\Utilities\Enum\EnumTranslatableInterface;
use Windwalker\Utilities\Enum\EnumTranslatableTrait;
use Windwalker\Utilities\Contract\LanguageInterface;

enum DeviceType: string implements EnumTranslatableInterface
{
    use EnumTranslatableTrait;

    case PC = 'pc';
    case TABLET = 'tablet';
    case PHONE = 'phone';
    case OTHERS = 'others';

    public function trans(LanguageInterface $lang, ...$args): string
    {
        return $lang->trans('app.device.type.' . $this->getKey());
    }
}
