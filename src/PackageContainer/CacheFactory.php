<?php

declare(strict_types=1);

namespace PackageInfo\PackageContainer;

use PackageInfo\PackageContainer;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class CacheFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): Cache
    {
        return new Cache(
            new PackageContainer(),
            $container->get('config')['cache_file_path'],
        );
    }
}
