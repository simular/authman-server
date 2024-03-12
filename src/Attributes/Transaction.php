<?php

declare(strict_types=1);

namespace App\Attributes;

use Windwalker\Core\Manager\DatabaseManager;
use Windwalker\DI\Attributes\AttributeHandler;
use Windwalker\DI\Attributes\ContainerAttributeInterface;

#[\Attribute]
class Transaction implements ContainerAttributeInterface
{
    public function __construct(protected ?string $connection = null)
    {
    }

    public function __invoke(AttributeHandler $handler): callable
    {
        return function () use ($handler) {
            $container = $handler->getContainer();
            $manager = $container->get(DatabaseManager::class);

            return $manager->get($this->connection)
                ->transaction(fn () => $handler());
        };
    }
}
