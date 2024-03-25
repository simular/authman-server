<?php

declare(strict_types=1);

namespace App\Enum;

use Windwalker\Core\Http\Exception\ApiErrorCodeTrait;
use Windwalker\Utilities\Attributes\Enum\Title;

enum ErrorCode: int
{
    use ApiErrorCodeTrait;

    // 401
    #[Title('Access Token expired')]
    case ACCESS_TOKEN_EXPIRED = 40101;

    #[Title('Refresh Token expired')]
    case REFRESH_TOKEN_EXPIRED = 40102;

    #[Title('Email or password incorrect.')]
    case INVALID_CREDENTIALS = 40103;

    #[Title('Invalid Session.')]
    case INVALID_SESSION = 40104;

    #[Title('Password changed.')]
    case PASSWORD_CHANGED = 40105;

    // 403
    #[Title('This email has been used.')]
    case USER_EMAIL_EXISTS = 40301;
}
