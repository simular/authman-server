<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\UserSecret;
use Lyrasoft\Luna\User\UserService;
use Windwalker\DI\Attributes\Service;
use Windwalker\Query\Exception\NoResultException;

use function Windwalker\Query\uuid2bin;

#[Service]
class ApiUserService extends UserService
{
    public function getUserSecret(mixed $conditions = null, bool $refresh = false): ?UserSecret
    {
        return $this->once(
            'user.secret.' . json_encode($conditions),
            function () use ($conditions) {
                if ($conditions === null) {
                    $user = $this->getUser();

                    if (!$user->isLogin()) {
                        return null;
                    }

                    $conditions = ['user_id' => uuid2bin($user->getId())];
                }

                return $this->orm->findOne(UserSecret::class, $conditions);
            },
            $refresh
        );
    }

    public function mustGetUserSecret(mixed $conditions = null, bool $refresh = false): UserSecret
    {
        return $this->getUserSecret($conditions, $refresh)
            ?? throw new NoResultException('User secret not found.');
    }

    /**
     * @return  array{ password: string, salt: string, verifier: string, secret: string, master: string }
     */
    public static function getTestSecrets(): array
    {
        return [
            'password' => '1234',
            'salt' => '5650da90c28fbddb2c12dd72652cb5dc',
            'kek' => 'hex:eb0d39c41b0c1993d9258472136f0b532a1eb731340fadc2a45446cb94b3f1cd',
            'secret' => '9owrNAfGv2JaiNxAIlps3g_8Bppp5Vt4lWqxl9o-1nr0LhR9vUEH9vvrHDMxtaouDrPPw79ZrePLTASdo-XeqjclR24cYcJ1K45alIT2hIFhKIeL_F4gbovYwcrMgt00YVI0d6iV6jD4XfKW9WGfhOu5T7zn9ylq0kVE1zgQMZFttX4jUOFhKDzuHgvRZnvI7_WPeeI2wYViJKA_',
            'master' => 'YdEgArusa8gGajicGrmY4f6xOGo6YXue_xCsD9m445hVaMgsDFwbwu1YvHmuGCwDtpBSK588P4n-b8JVIvxf7lnvTr9oC5yHH2dFrAizigjzlxLfZGnWtV1i8JpzHoVRmQv6qH3R_80EgZvPgj-cnCGcI9SDnOkrRhBDjwuwd5hkQ3FE-rtsF0nAuF2vrKBS12DNI5utKHK7xekvV4uYYG8ow7FbQu0mRbpMmT6S_ZCmkksdPwsOzyNkMMs='
        ];
    }
}
