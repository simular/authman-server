<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Account;
use App\Entity\User;
use Windwalker\Core\Form\Exception\ValidateFailException;
use Windwalker\DI\Attributes\Service;
use Windwalker\ORM\ORM;
use Windwalker\Utilities\Cache\InstanceCacheTrait;

use function Windwalker\Query\uuid2bin;

#[Service]
class AccountService
{
    use InstanceCacheTrait;

    public function __construct(protected ORM $orm)
    {
    }

    public function countAccounts(User $user): int
    {
        return (int) $this->orm->from(Account::class)
            ->selectRaw('COUNT(id) AS count')
            ->where('user_id', uuid2bin($user->getId()))
            ->result();
    }

    public function validateUserNotExceedAccountLimit(User $user): void
    {
        $limit = (int) env('MAX_ACCOUNTS');

        if ($limit <= 0) {
            return;
        }

        $count = $this->countAccounts($user);

        if ($count >= $limit) {
            throw new ValidateFailException(
                'You have exceeded the maximum number of available account tokens: ' . $limit
            );
        }
    }
}
