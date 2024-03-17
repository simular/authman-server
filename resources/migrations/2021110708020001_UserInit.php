<?php

declare(strict_types=1);

namespace App\Migration;

use App\Entity\User;
use App\Entity\UserRoleMap;
use App\Entity\UserSecret;
use App\Service\ApiUserService;
use App\Service\EncryptionService;
use Brick\Math\BigInteger;
use Lyrasoft\Luna\Auth\SRP\SRPService;
use Lyrasoft\Luna\Entity\Session;
use Lyrasoft\Luna\Entity\UserRole;
use Lyrasoft\Luna\Entity\UserSocial;
use Lyrasoft\Luna\User\UserService;
use Windwalker\Core\Console\ConsoleApplication;
use Windwalker\Core\Migration\Migration;
use Windwalker\Crypt\SecretToolkit;
use Windwalker\Database\Schema\Schema;
use Windwalker\ORM\ORM;
use Windwalker\SRP\Step\PasswordFile;

/**
 * Migration UP: 2021110708010001_UserInit.
 *
 * @var Migration $mig
 * @var ConsoleApplication $app
 */
$mig->up(
    static function (
        UserService $userService,
        ORM $orm,
        SRPService $srpService,
        EncryptionService $encryptionService
    ) use (
        $mig
    ) {
        // User
        $mig->createTable(
            User::class,
            function (Schema $schema) {
                $schema->primaryUuidBinary('id');
                $schema->varchar('email')->comment('Email');
                $schema->varchar('name')->comment('Name');
                $schema->varchar('avatar')->comment('Avatar');
                $schema->varchar('password')->length(1024)->comment('Password');
                $schema->varchar('sess_valid_from');
                $schema->tinyint('enabled')->comment('0: disabled, 1: enabled');
                $schema->tinyint('verified')->comment('0: unverified, 1: verified');
                $schema->varchar('activation')->comment('Activation code.');
                $schema->tinyint('receive_mail')->defaultValue(0)->length(1);
                $schema->varchar('reset_token')->comment('Reset Token');
                $schema->datetime('last_reset')->nullable(true)->comment('Last Reset Time');
                $schema->datetime('last_login')->nullable(true)->comment('Last Login Time');
                $schema->datetime('registered')->nullable(true)->comment('Register Time');
                $schema->datetime('modified')->nullable(true)->comment('Modified Time');
                $schema->json('params')->comment('Params');

                $schema->addUniqueKey('email');
            }
        );

        // User Secret
        $mig->createTable(
            UserSecret::class,
            function (Schema $schema) {
                $schema->uuidBinary('user_id');
                $schema->varchar('secret')->length(512);
                $schema->varchar('master')->length(512);
                $schema->varchar('server_secret');

                $schema->addIndex('user_id');
            }
        );

        // User Group
        $mig->createTable(
            UserRole::class,
            function (Schema $schema) {
                $schema->primary('id')->comment('Primary Key');
                $schema->integer('parent_id')->comment('Parent ID');
                $schema->integer('lft')->comment('Left Key');
                $schema->integer('rgt')->comment('Right key');
                $schema->integer('level')->comment('Nested Level');
                $schema->varchar('title')->comment('Title');
                $schema->longtext('description')->comment('Description');
                $schema->tinyint('state')->length(1)->comment('0: unpublished, 1:published');
                $schema->datetime('created')->nullable(true)->comment('Created Date');
                $schema->datetime('modified')->nullable(true)->comment('Modified Date');
                $schema->integer('created_by')->comment('Author');
                $schema->integer('modified_by')->comment('Modified User');
                $schema->json('params')->comment('Params');

                $schema->addIndex(['lft', 'rgt']);
                $schema->addIndex('created_by');
            }
        );

        $mig->createTable(
            UserRoleMap::class,
            function (Schema $schema) {
                $schema->uuidBinary('user_id');
                $schema->varchar('role_id');

                $schema->addIndex('user_id');
                $schema->addIndex('role_id');
                $schema->addPrimaryKey(['user_id', 'role_id']);
                $schema->addIndex(['user_id', 'role_id']);
            }
        );

        // User Social
        $mig->createTable(
            UserSocial::class,
            function (Schema $schema) {
                $schema->uuidBinary('user_id')->comment('User ID');
                $schema->varchar('identifier')->comment('User identifier name');
                $schema->char('provider')->length(20)->comment('Social provider');
                $schema->json('params')->comment('Params');

                $schema->addIndex('user_id');
                $schema->addIndex('identifier');
            }
        );

        // Session
        $mig->createTable(
            Session::class,
            function (Schema $schema) {
                $schema->varchar('id')->length(192);
                $schema->text('data');
                $schema->uuidBinary('user_id');
                $schema->tinyint('remember');
                $schema->integer('time');

                $schema->addUniqueKey('id');
                $schema->addIndex('user_id');
                $schema->addIndex('time');
            }
        );

        $user = new User();

        $user->setEmail('webadmin@simular.co');
        $user->setName('Simular');
        $user->setAvatar('https://avatars.githubusercontent.com/u/13175487#.jpg');
        $user->setEnabled(true);
        $user->setVerified(true);
        $user->setReceiveMail(true);

        $secrets = ApiUserService::getTestSecrets();
        $pass = $secrets['password'];
        $salt = BigInteger::fromBase($secrets['salt'], 16);
        $secret = $secrets['secret'];
        $master = $secrets['master'];

        $client = $srpService->getSRPClient();
        $x = $client->generatePasswordHash(
            $salt,
            $user->getEmail(),
            $pass
        );

        // (g^x % N)
        $verifier = $client->generateVerifier($x);

        $pf = new PasswordFile($salt, $verifier);

        $password = $srpService::encodePasswordVerifier($pf->salt, $pf->verifier);
        $user->setPassword($password);

        /** @var User $user */
        $user = $orm->createOne(User::class, $user);

        $userSecret = $user->getSecretEntity(true);
        $userSecret->setSecret($secret);
        $userSecret->setMaster($master);

        $orm->updateOne($userSecret);

        $map = new UserRoleMap();
        $map->setUserId($user->getId());
        $map->setRoleId('superuser');

        $orm->createOne($map::class, $map);
    }
);

/**
 * Migration DOWN.
 */
$mig->down(
    static function () use ($mig) {
        $mig->dropTables(
            User::class,
            UserSecret::class,
            UserSocial::class,
            UserRole::class,
            UserRoleMap::class,
            Session::class
        );
    }
);
