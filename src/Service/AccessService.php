<?php

declare(strict_types=1);

namespace App\Service;

use Lyrasoft\Luna\Entity\Rule;
use Lyrasoft\Luna\Entity\UserRole;
use Lyrasoft\Luna\Entity\UserRoleMap;
use Lyrasoft\Luna\LunaPackage;
use Lyrasoft\Luna\Services\UserSwitchService;
use Lyrasoft\Luna\Tree\Node;
use Lyrasoft\Luna\Tree\NodeInterface;
use Lyrasoft\Luna\Tree\TreeBuilder;
use Lyrasoft\Luna\User\UserEntityInterface;
use Lyrasoft\Luna\User\UserService;
use Windwalker\Authorization\AuthorizationInterface;
use Windwalker\Core\Application\AppClient;
use Windwalker\Core\Application\ApplicationInterface;
use Windwalker\ORM\ORM;
use Windwalker\Query\Query;
use Windwalker\Session\Session;
use Windwalker\Utilities\Cache\InstanceCacheTrait;

use function Windwalker\collect;
use function Windwalker\Query\uuid2bin;
use function Windwalker\value;

class AccessService
{
    use InstanceCacheTrait;

    public const SUPERUSER_ACTION = 'super.user';

    public const ADMIN_ACCESS_ACTION = 'admin.access';

    public const ROLE_MODIFY_ACTION = 'role.modify';

    public function __construct(
        protected ApplicationInterface $app,
        protected AuthorizationInterface $authorization,
        protected ORM $orm,
        protected Session $session
    ) {
    }

    public function can(string $action, mixed $user = null, ...$args): bool
    {
        return $this->check($action, $user, ...$args);
    }

    public function check(string $action, mixed $user = null, ...$args): bool
    {
        $currentUser = $this->getUser($user);

        if (
            ($user === null || $user->getId() === $currentUser?->getId())
            && $action === static::ADMIN_ACCESS_ACTION
            && $this->isAdminUserSwitched()
        ) {
            return true;
        }

        if ($this->authorization->hasPolicy($action)) {
            return $this->authorization->authorize($action, $user, ...$args);
        }

        $user = $this->getUser($user ?? $currentUser);
        $userId = $user->getId();

        // Get roles
        $roles = $this->getUserRoles($userId);

        // Check for Super User
        if ($this->checkRolesAllowAction($roles, static::SUPERUSER_ACTION)) {
            return true;
        }

        // Check action rules
        if ($this->checkRolesAllowAction($roles, $action)) {
            return true;
        }

        return false;
    }

    /**
     * checkRolesAllowAction
     *
     * @param  string  $action
     *
     * @return  array<UserRole>
     */
    public function getRolesAllowAction(string $action): array
    {
        $roles = $this->getRoles();

        $allowed = [];

        // Check permissions
        foreach ($roles as $role) {
            if ($this->checkRoleAllowAction($role, $action) === true) {
                $allowed[] = $role;
            }
        }

        return $allowed;
    }

    public function checkRolesAllowAction(array $roles, string $action): bool
    {
        $rules = $this->getRules($action);

        // Check permissions
        foreach ($roles as $role) {
            if ($this->checkRoleAllowForRules($role, $rules) === true) {
                return true;
            }
        }

        return false;
    }

    public function checkRoleAllowAction(UserRole $role, string $action): bool
    {
        $rules = $this->getRules($action);

        return (bool) $this->checkRoleAllowForRules($role, $rules);
    }

    /**
     * checkRoleAllow
     *
     * @param  UserRole     $role
     * @param  array<Rule>  $rules
     *
     * @return  bool|null
     */
    public function checkRoleAllowForRules(UserRole $role, array $rules): ?bool
    {
        $allow = null;

        // Check rules access
        foreach ($rules as $rule) {
            if ((string) $rule->getRoleId() === (string) $role->getId()) {
                $allow = $rule->getAllow();

                if ($allow === false) {
                    return false;
                }
            }
        }

        $parent = $this->getRoleNodeById($role->getId())?->getParent()?->getValue();

        if ($parent) {
            $parentAllow = $this->checkRoleAllowForRules($parent, $rules);

            // If parent is deny, always deny.
            if ($parentAllow === false) {
                return false;
            }

            // If self is inherited, use parent permission.
            if ($allow === null) {
                return $parentAllow;
            }
        }

        // No parent, just return self, and NULL will consider as deny.
        return $allow;
    }

    /**
     * getUserRoles
     *
     * @param  mixed  $user
     *
     * @return  array<UserRole>
     */
    public function getUserRoles(mixed $user = null): array
    {
        $user = $this->getUser($user);

        $id = $user->getId();

        return $this->once(
            'user.roles.' . $id,
            function () use ($id) {
                $roleIds = $this->orm->findColumn(UserRoleMap::class, 'role_id', ['user_id' => uuid2bin($id)])->dump();
                $roleTree = $this->getRolesTree();

                $matches = [];

                foreach ($roleTree->iterate() as $roleNode) {
                    /** @var UserRole $role */
                    $role = $roleNode->getValue();

                    if ($role && in_array($role->getId(), $roleIds)) {
                        $matches[] = $role;
                    }
                }

                return $matches;
            }
        );
    }

    /**
     * @param  mixed                      $user
     * @param  array<UserRoleMap|string>  $roleMaps
     *
     * @return  array<UserRoleMap>
     */
    public function addRoleMapsToUser(mixed $user = null, mixed $roleMaps = []): array
    {
        $currentRoleIds = collect($this->getUserRoles($user))
            ->map(fn(UserRole $map) => $map->getId())
            ->map('strval');

        $user = $this->getUser($user);

        $maps = [];

        if (!is_array($roleMaps)) {
            $roleMaps = [$roleMaps];
        }

        foreach ($roleMaps as $roleMap) {
            if (!$roleMap instanceof UserRoleMap) {
                $map = new UserRoleMap();
                $map->setUserId($user->getId());
                $map->setRoleId($roleMap);

                $roleMap = $map;
            }

            if ($currentRoleIds->contains($roleMap->getRoleId())) {
                continue;
            }

            $maps[] = $this->orm->createOne(UserRoleMap::class, $roleMap);
        }

        return $maps;
    }

    /**
     * @param  mixed                   $user
     * @param  array<UserRole|string>  $roles  Can be an array or single role string|int|entity|enum
     * @param  array                   $extra
     *
     * @return  array<UserRoleMap>
     * @throws \ReflectionException
     */
    public function addRolesToUser(mixed $user = null, mixed $roles = [], array $extra = []): array
    {
        $currentRoleIds = collect($this->getUserRoles($user))
            ->map(fn(UserRole $map) => $map->getId())
            ->map('strval');

        $user = $this->getUser($user);

        $maps = [];

        if (!is_array($roles)) {
            $roles = [$roles];
        }

        foreach ($roles as $role) {
            $roleId = $this->unwrapRole($role);

            if ($currentRoleIds->contains($roleId)) {
                continue;
            }

            $map = new UserRoleMap();
            $map->setUserId($user->getId());
            $map->setRoleId($roleId);

            if ($extra !== []) {
                $map = $this->orm->hydrateEntity($extra, $map);
            }

            $maps[] = $this->orm->createOne(UserRoleMap::class, $map);
        }

        return $maps;
    }

    public function removeRoleFromUser(mixed $user = null, mixed $roles = []): void
    {
        $user = $this->getUser($user);

        if (!is_array($roles)) {
            $roles = [$roles];
        }

        if ($roles === []) {
            return;
        }

        $roleIds = array_map([$this, 'unwrapRole'], $roles);

        $this->orm->deleteWhere(
            UserRoleMap::class,
            [
                'user_id' => uuid2bin($user->getId()),
                'role_id' => $roleIds,
            ]
        );
    }

    public function userIsRole(mixed $user = null, mixed $role = null): bool
    {
        $user ??= $this->getUser();
        $roleId = $this->unwrapRole($role);

        $roles = $this->getUserRoles($user);

        foreach ($roles as $userRole) {
            if ((string) $userRole->getId() === (string) $roleId) {
                return true;
            }
        }

        return false;
    }

    public function userInRoles(mixed $user = null, mixed $roles = []): bool
    {
        $user ??= $this->getUser();

        if (!is_array($roles)) {
            $roles = [$roles];
        }

        foreach ($roles as $role) {
            if ($this->userIsRole($user, $role)) {
                return true;
            }
        }

        return false;
    }

    public function isSuperUser(mixed $user = null): bool
    {
        return $this->check(static::SUPERUSER_ACTION, $user);
    }

    public function isSuperUserRole(mixed $role): bool
    {
        return $this->checkRoleAllowAction(
            $this->wrapUserRole($role),
            static::SUPERUSER_ACTION
        );
    }

    public function isParentRole(UserRole|string|int $targetRole, UserRole|string|int $role): bool
    {
        $role = (string) $this->unwrapRole($role);
        $targetRole = (string) $this->unwrapRole($targetRole);

        $roleNode = $this->getRoleNodeById($role);

        if ($roleNode->isRoot()) {
            return false;
        }

        $targetNode = $this->getRoleNodeById($targetRole);

        if ($targetNode->isLeaf()) {
            return false;
        }

        foreach ($roleNode->getAncestors() as $ancestor) {
            if ($ancestor->isRoot()) {
                continue;
            }

            if ($ancestor->getValue()->getId() === $targetNode->getValue()->getId()) {
                return true;
            }
        }

        return false;
    }

    public function isChildRole(UserRole|string|int $targetRole, UserRole|string|int $role): bool
    {
        $role = (string) $this->unwrapRole($role);
        $targetRole = (string) $this->unwrapRole($targetRole);

        $roleNode = $this->getRoleNodeById($role);

        if ($roleNode->isRoot()) {
            return false;
        }

        $targetNode = $this->getRoleNodeById($targetRole);

        if ($targetNode->isLeaf()) {
            return false;
        }

        foreach ($targetNode->getAncestors() as $ancestor) {
            if ($ancestor->isRoot()) {
                continue;
            }

            if ($ancestor->getValue()->getId() === $roleNode->getValue()->getId()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  mixed  $user
     *
     * @return  array<UserRole>
     */
    public function getAllowedRolesForUser(mixed $user = null): array
    {
        $user ??= $this->getUser();
        $roles = $this->getRoles();
        $userRoles = $this->getUserRoles($user);
        $allowed = [];

        foreach ($userRoles as $userRole) {
            foreach ($roles as $role) {
                if (
                    !isset($allowed[$role->getId()])
                    && (
                        $this->unwrapRole($userRole) === $this->unwrapRole($role)
                        || $this->isChildRole($userRole, $role)
                    )
                ) {
                    $allowed[$role->getId()] = $role;
                }
            }
        }

        return $allowed;
    }

    /**
     * getRoles
     *
     * @return  NodeInterface|NodeInterface[]
     */
    public function getRolesTree(): NodeInterface
    {
        return $this->once(
            'roles.tree',
            function () {
                $roles = $this->loadStaticRoles();

                if ($this->app->config('access.roles_db_enabled') ?? false) {
                    $dbRoles = $this->loadDBRoles();

                    // Merge nodes
                    foreach ($dbRoles->getChildren() as $child) {
                        $roles->addChild($child);
                    }
                }

                return $roles;
            }
        );
    }

    /**
     * getRoles
     *
     * @return  array<UserRole>
     */
    public function getRoles(): array
    {
        return $this->once(
            'roles',
            function () {
                $roles = [];

                foreach ($this->getRoleFlatNodes() as $id => $node) {
                    $roles[$id] = $node->getValue();
                }

                return $roles;
            }
        );
    }

    /**
     * getRoleFlatNodes
     *
     * @return  array<NodeInterface>
     */
    public function getRoleFlatNodes(): array
    {
        return $this->once(
            'role.nodes',
            function () {
                $roles = [];

                foreach ($this->getRolesTree()->iterate() as $node) {
                    $role = $node->getValue();

                    if ($role) {
                        $roles[$role->getId()] = $node;
                    }
                }

                return $roles;
            }
        );
    }

    public function getRoleNodeById(string|int $id): ?NodeInterface
    {
        return $this->getRoleFlatNodes()[$id] ?? null;
    }

    public function loadStaticRoles(): NodeInterface
    {
        return $this->once(
            'roles.static',
            fn() => new Node(
                null,
                static::injectIdToRoles(static::getConfigRoles())
            )
        );
    }

    protected function getConfigRoles(): array
    {
        return $this->cacheStorage['roles.static'] ??= $this->stripValue($this->app->config('access.roles')) ?: [];
    }

    protected function stripValue(mixed $value): mixed
    {
        if ($value instanceof \Closure) {
            return $this->app->call($value);
        }

        return $value;
    }

    public function loadDBRoles(): NodeInterface
    {
        return $this->once(
            'roles.db',
            function () {
                $items = $this->orm->findList(UserRole::class)->all();

                return TreeBuilder::create(
                    $items,
                    static fn(UserRole $role) => $role->getId(),
                    static fn(UserRole $role) => $role->getParentId(),
                );
            }
        );
    }

    /**
     * getRules
     *
     * @return  array<Rule>
     */
    public function getRules(string $action): array
    {
        return $this->once(
            'rules:' . $action,
            function () use ($action) {
                [$ns, $act, $id] = static::extractAction($action);
                $result = [];

                // Static rules
                $rules = $this->loadStaticRules();

                if ($this->app->config('access.actions_db_enabled') ?? false) {
                    // DB Rules
                    $rules = array_merge(
                        $rules,
                        $this->loadDBRules($action)
                    );
                }

                if ($id) {
                    foreach ($rules as $rule) {
                        if (
                            $rule->getType() === $ns
                            && $rule->getAction() === $act
                            && (string) $rule->getTargetId() === $id
                        ) {
                            $result[] = $rule;
                        }
                    }
                }

                if ($ns) {
                    foreach ($rules as $rule) {
                        // Get global access
                        if ($rule->getType() === $ns && $rule->getAction() === $act && !$rule->getTargetId()) {
                            $result[] = $rule;
                        }
                    }
                }

                foreach ($rules as $rule) {
                    // Get global access
                    if (!$rule->getType() && $rule->getAction() === $act && !$rule->getTargetId()) {
                        $result[] = $rule;
                    }
                }

                return $result;
            }
        );
    }

    public function wrapUserRole(string|int|UserRole $role): ?UserRole
    {
        if ($role instanceof UserRole) {
            return $role;
        }

        foreach ($this->getRoles() as $userRole) {
            if ((string) $userRole->getId() === (string) $role) {
                return $userRole;
            }
        }

        return null;
    }

    public function unwrapRole(mixed $role): ?string
    {
        if ($role === null) {
            return null;
        }

        if ($role instanceof UserRole) {
            return $role->getId();
        }

        if ($role instanceof UserRoleMap) {
            return (string) $role->getRoleId();
        }

        return (string) value($role);
    }

    /**
     * loadStaticRules
     *
     * @return  array<Rule>
     */
    public function loadStaticRules(): array
    {
        return $this->once(
            'rules.static',
            function () {
                $result = [];
                $actions = $this->app->config('access.actions');

                foreach ($actions as $name => $rules) {
                    foreach ($rules as $roleId => $allow) {
                        [$ns, $action, $id] = static::extractAction($name);

                        $rule = new Rule();
                        $rule->setRoleId($roleId);
                        $rule->setType((string) $ns);
                        $rule->setName($name);
                        $rule->setTargetId($id ?? '');
                        $rule->setAction($action ?? '');
                        $rule->setAllow($allow);

                        $result[] = $rule;
                    }
                }

                return $result;
            }
        );
    }

    public function loadDBRules(string $action): array
    {
        return $this->once(
            'rules.db:' . $action,
            function () use ($action) {
                [$ns, $act, $id] = static::extractAction($action);

                return $this->orm->from(Rule::class)
                    ->where('name', $action)
                    ->orWhere(
                        function (Query $query) use ($ns, $act, $id) {
                            if ($id) {
                                $query->whereRaw('type = :ns AND action = :action AND target_id = :target')
                                    ->bind('ns', $ns)
                                    ->bind('action', $act)
                                    ->bind('target', $id);

                                return;
                            }

                            if ($ns) {
                                $query->whereRaw('type = :ns AND action = :action AND target_id = :target')
                                    ->bind('ns', $ns)
                                    ->bind('action', $act)
                                    ->bind('target', (string) $id);

                                return;
                            }

                            $query->whereRaw('type = \'\' AND action = :action AND target_id = \'\'')
                                ->bind('action', $act);
                        }
                    )
                    ->all(Rule::class)
                    ->dump();
            }
        );
    }

    /**
     * injectIdToRoles
     *
     * @param  iterable<NodeInterface>  $nodes
     *
     * @return  iterable<NodeInterface>
     */
    public static function injectIdToRoles(iterable $nodes): iterable
    {
        foreach ($nodes as $roleId => $child) {
            if ($child instanceof UserRole && is_array($nodes)) {
                $nodes[$roleId] = $child = new Node($child);
            }

            if ($child instanceof NodeInterface && ($role = $child->getValue()) instanceof UserRole) {
                /** @var UserRole $role */
                $role->setId($roleId);
            }
        }

        return $nodes;
    }

    /**
     * extractAction
     *
     * @param  string  $action
     *
     * @return  array<?string>
     */
    protected static function extractAction(string $action): array
    {
        $extracted = explode('::', $action, 3);

        return match (count($extracted)) {
            1 => [null, $extracted[0], null],
            2 => [$extracted[0], $extracted[1], null],
            3 => $extracted
        };
    }

    /**
     * isUserSwitched
     *
     * @return  bool
     */
    protected function isAdminUserSwitched(): bool
    {
        if ($this->app->getClient() === AppClient::WEB) {
            $userSwitcher = $this->app->service(UserSwitchService::class);
            $luna = $this->app->service(LunaPackage::class);

            if ($luna->isAdmin() && $userSwitcher->getOriginUserId()) {
                return true;
            }
        }

        return false;
    }

    public function getUser(mixed $conditions = null): ?UserEntityInterface
    {
        if ($conditions instanceof UserEntityInterface) {
            return $conditions;
        }

        return $this->app->service(UserService::class)->getUser($conditions);
    }

    /**
     * @return  array<UserRole>
     */
    public function getBasicRoles(): array
    {
        $roles = $this->app->config('access.basic_roles') ?: [];

        if (!is_array($roles)) {
            $roles = [$roles];
        }

        return array_map([$this, 'wrapUserRole'], $roles);
    }

    /**
     * @return  array<string|\BackedEnum>
     */
    public function getSelectableRoles(): array
    {
        return $this->app->config('access.selectable_roles') ?: [];
    }

    public function canSelectUserRoles(): bool
    {
        $roles = $this->getSelectableRoles();

        return $roles !== [] && $this->check(static::ROLE_MODIFY_ACTION);
    }
}
