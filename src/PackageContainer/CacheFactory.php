<?php

declare(strict_types=1);

namespace PackageInfo\PackageContainer;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class CacheFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): Cache
    {
        return new Cache(
            $container->get('config')['cache_file_path']
        );
    }
}
