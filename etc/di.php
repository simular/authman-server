<?php

declare(strict_types=1);

use App\Enum\ErrorCode;
use Lyrasoft\Luna\User\UserService;
use Windwalker\Console\CommandWrapper;
use Windwalker\Core\Attributes\Controller;
use Windwalker\Core\Attributes\Csrf;
use Windwalker\Core\Attributes\Json;
use Windwalker\Core\Attributes\JsonApi;
use Windwalker\Core\Attributes\Module;
use Windwalker\Core\Attributes\Ref;
use Windwalker\Core\Attributes\ViewModel;
use Windwalker\DI\Attributes\AttributeType;
use Windwalker\DI\Attributes\Autowire;
use Windwalker\DI\Attributes\Decorator;
use Windwalker\DI\Attributes\Inject;
use Windwalker\DI\Attributes\Service;
use Windwalker\DI\Attributes\Setup;
use Windwalker\DI\Container;
use Windwalker\Utilities\Arr;

use function Windwalker\include_arrays;

class_alias(\App\Entity\User::class, CurrentUser::class);

return Arr::mergeRecursive(
    // Load with namespace,
    [
        'factories' => include_arrays(__DIR__ . '/di/*.php'),
        'providers' => [
            //
        ],
        'bindings' => [
            CurrentUser::class => function (Container $container) {
                $user = $container->get(UserService::class)->getCurrentUser();

                if (!$user->isLogin()) {
                    ErrorCode::USER_NOT_FOUND->throw();
                }

                return $user;
            },
        ],
        'aliases' => [
            //
        ],
        'layouts' => [
            //
        ],
        'attributes' => [
            //
        ]
    ]
);
