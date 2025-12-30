<?php

declare(strict_types=1);

namespace PackageInfo;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function file_exists;
use function file_get_contents;

final readonly class PackageContainerFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): PackageContainer
    {
        $packageContainer = new PackageContainer();

        $cacheFilePath = $container->get('config')['cache_file_path'] ?? '';
        if (file_exists($cacheFilePath)) {
            $cacheContent = file_get_contents($cacheFilePath);
            $packageContainer->unserialize($cacheContent);
        }

        return $packageContainer;
    }
}
