<?php

declare(strict_types=1);

namespace App\Module\Api;

use App\Entity\Account;
use App\Repository\AccountRepository;
use Lyrasoft\Luna\User\UserService;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\Controller;
use Windwalker\Data\Collection;
use Windwalker\DI\Attributes\Autowire;

use function Windwalker\Query\uuid2bin;

#[Controller]
class AccountController
{
    public function items(
        AppContext $app,
        #[Autowire]
        AccountRepository $repository,
        \CurrentUser $currentUser
    ): array {
        [
            $q,
            $page
        ] = $app->input('q', 'page');

        $q = (string) $q;
        $page = min(1, (int) $page);

        $items = $repository->getApiListSelector()
            ->searchTextFor(
                $q,
                [
                    'account.title',
                    'account.url'
                ]
            )
            ->where('user_id', uuid2bin($currentUser->getId()))
            ->order('account.created', 'DESC')
            ->page($page)
            ->all(Account::class);

        return compact(
            'items'
        );
    }
}
