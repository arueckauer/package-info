<?php

declare(strict_types=1);

namespace PackageInfo\PackageContainer;

use Psr\Container\ContainerInterface;

class CacheFactory
{
    public function __invoke(ContainerInterface $container): Cache
    {
        return new Cache(
            $container->get('config')['cache_file_path']
        );
    }
}
