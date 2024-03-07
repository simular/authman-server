<?php

/**
 * Part of starter project.
 *
 * @copyright  Copyright (C) 2021 __ORGANIZATION__.
 * @license    __LICENSE__
 */

declare(strict_types=1);

namespace App\Enum;

use MyCLabs\Enum\Enum;
use Windwalker\Utilities\Attributes\Enum\Title;
use Windwalker\Utilities\Enum\EnumTranslatableInterface;
use Windwalker\Utilities\Enum\EnumTranslatableTrait;
use Windwalker\Utilities\Contract\LanguageInterface;

/**
 * The BannerType enum class.
 *
 * @method static $this HOME()
 */
class BannerType extends Enum implements EnumTranslatableInterface
{
    use EnumTranslatableTrait;

    #[Title('首頁橫幅')]
    public const HOME = 'home';

    /**
     * Creates a new value of some type
     *
     * @psalm-pure
     *
     * @param  mixed  $value
     *
     * @psalm-param T $value
     * @throws \UnexpectedValueException if incompatible type is given.
     */
    public function __construct(mixed $value)
    {
        parent::__construct($value);
    }

    public function trans(LanguageInterface $lang, ...$args): string
    {
        return $lang->trans('app.banner.type.' . $this->getKey());
    }
}
