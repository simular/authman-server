<?php

declare(strict_types=1);

namespace App\Auth;

use Lyrasoft\Luna\Entity\User;
use Lyrasoft\Luna\User\Handler\UserHandlerInterface;
use Lyrasoft\Luna\User\UserEntityInterface;
use Ramsey\Uuid\UuidInterface;
use Windwalker\Core\Attributes\Ref;
use Windwalker\Data\Collection;
use Windwalker\ORM\EntityMapper;
use Windwalker\ORM\ORM;
use Windwalker\Session\Handler\DatabaseHandler;
use Windwalker\Session\Session;
use Windwalker\Utilities\Cache\InstanceCacheTrait;

use function Windwalker\Query\uuid2bin;

class UserHandler implements UserHandlerInterface
{
    use InstanceCacheTrait;

    /**
     * UserService constructor.
     */
    public function __construct(
        protected Session $session,
        protected ORM $orm,
        #[Ref('user')]
        protected array $config
    ) {
    }

    public function load(mixed $conditions = null): ?UserEntityInterface
    {
        $mapper = $this->getMapper();

        if ($conditions instanceof UuidInterface) {
            $conditions = uuid2bin($conditions);
        }

        $sessUserId = $this->session->get('login_user_id');

        // If session user id same as conditions
        // Just get current user
        if (
            $conditions
            && $sessUserId
            && is_scalar($conditions)
            && (string) $conditions === (string) $sessUserId
        ) {
            $conditions = null;
        }

        if (!$conditions) {
            $user = $this->once(
                'current.user',
                function () use ($sessUserId, $mapper) {
                    if (!$sessUserId) {
                        return false;
                    }

                    $pk = $mapper->getMainKey();

                    // If user is logged-in, get user data from DB to refresh info.
                    $user = $mapper->findOne([$pk => uuid2bin($sessUserId)], Collection::class);

                    if (!$user) {
                        return false;
                    }

                    unset($user->password);
                    $loginUser = $user->dump();

                    return $this->handleFoundUser($loginUser, $mapper);
                }
            );

            if (!$user) {
                return null;
            }

            return $user;
        }

        if (is_array($conditions) && isset($conditions['email'])) {
            $conditions['email'] = idn_to_ascii($conditions['email']);
        }

        $user = $mapper->findOne($conditions, Collection::class);

        if (!$user) {
            return null;
        }

        $user = $user?->dump(true) ?? [];

        return $this->handleFoundUser($user, $mapper);
    }

    private function handleFoundUser(array $user, EntityMapper $mapper)
    {
        if ($user['email'] ?? null) {
            $user['email'] = idn_to_utf8($user['email']);
        }

        /** @var User $user */
        return $mapper->toEntity($user);
    }

    public function login(mixed $user, array $options = []): bool
    {
        $mapper = $this->getMapper();

        if ($user instanceof UserEntityInterface) {
            $userId = $user->getId();
        } else {
            $user = $mapper->toCollection($user);
            $pk = $mapper->getMainKey();
            $userId = $user->$pk;
        }

        $this->session->set('login_user_id', (string) $userId);

        $this->cacheReset();

        $sessHandler = $this->session->getBridge()->getHandler();

        if ($sessHandler instanceof DatabaseHandler) {
            $table = $sessHandler->getOption('table');

            if ($this->orm->getDb()->getTable($table)->hasColumn('user_id')) {
                $this->orm->getDb()->update($table)
                    ->set('user_id', uuid2bin($userId))
                    ->set('remember', (int) ($options['remember'] ?? 0))
                    ->where('id', $this->session->getId())
                    ->execute();
            }
        }

        return true;
    }

    public function logout(mixed $user = null): bool
    {
        $session = $this->session;

        $session->start();

        $session->destroy();
        $session->regenerate(false, false);

        $this->cacheReset();

        return true;
    }

    /**
     * getMapper
     *
     * @return  EntityMapper
     *
     * @throws ReflectionException
     *
     * @since  2.0.0
     */
    protected function getMapper(): EntityMapper
    {
        return $this->orm->mapper($this->getUserEntityClass());
    }

    /**
     * getUserEntityClass
     *
     * @return  string|T
     */
    public function getUserEntityClass(): string
    {
        return $this->config['entity'] ?? User::class;
    }

    /**
     * createUserEntity
     *
     * @param  array  $data  *
     *
     * @return  object|T
     *
     * @throws ReflectionException
     */
    public function createUserEntity(array $data = []): object
    {
        return $this->orm->createEntity($this->getUserEntityClass(), $data);
    }

    /**
     * @return User|null
     */
    public function getCurrent(): ?User
    {
        return $this->cacheGet('current.user');
    }

    /**
     * @param  User|null  $current
     *
     * @return  static  Return self to support chaining.
     */
    public function setCurrent(?User $current): static
    {
        $this->cacheSet('current.user', $current);

        return $this;
    }
}
