<?php

declare(strict_types=1);

namespace App\Module\Front\Auth;

use Generator;
use Windwalker\Core\Runtime\Config;
use Windwalker\DI\Attributes\Inject;

/**
 * The SocialLoginViewTrait class.
 */
trait SocialLoginViewTrait
{
    #[Inject]
    protected Config $config;

    public function hasSocialProviders(): bool
    {
        $providers = iterator_to_array($this->getSocialProviders());

        $enables = array_column($providers, 'enabled');

        return in_array(true, $enables, true);
    }

    public function getSocialProviders(): Generator
    {
        foreach ($this->config->getDeep('social_login.social_providers') ?? [] as $name => $item) {
            if ($item['enabled'] ?? false) {
                yield $name => $item;
            }
        }
    }
}
