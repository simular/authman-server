<?php

declare(strict_types=1);

namespace App\Module\Api;

use App\Entity\User;
use Psr\Container\ContainerExceptionInterface;
use Windwalker\Core\Application\AppContext;
use Windwalker\Core\Attributes\Controller;
use Windwalker\Core\Http\RequestAssert;
use Windwalker\ORM\ORM;

use function Windwalker\chronos;
use function Windwalker\Query\uuid2bin;

#[Controller]
class UserController
{
    public function refreshSessions(\CurrentUser $currentUser, ORM $orm): true
    {
        $orm->updateBatch(
            User::class,
            [
                'sess_valid_from' => chronos()
            ],
            ['id' => $currentUser->getId()]
        );

        return true;
    }

    /**
     * @param  \CurrentUser  $currentUser
     *
     * @return  \CurrentUser
     *
     * @deprecated  Use user/me instead.
     */
    public function me(\CurrentUser $currentUser): \CurrentUser
    {
        return $currentUser;
    }

    /**
     * @param  AppContext    $app
     * @param  ORM           $orm
     * @param  \CurrentUser  $user
     *
     * @return true
     *
     * @throws ContainerExceptionInterface
     * @throws \ReflectionException
     * @deprecated  Use user/deleteMe instead.
     */
    public function deleteMe(
        AppContext $app,
        ORM $orm,
        \CurrentUser $user,
    ): true {
        [
            $A,
            $M1,
            $sess,
        ] = $app->input(
            'A',
            'M1',
            'sess',
        )->values();

        RequestAssert::assert($A, 'Invalid credentials');
        RequestAssert::assert($M1, 'Invalid credentials');

        $app->call(
            $this->srpValidate(...),
            compact(
                'user',
                'A',
                'M1',
                'sess'
            )
        );

        // Delete User
        $orm->deleteWhere(User::class, ['id' => uuid2bin($user->getId())]);

        return true;
    }
}
