<?php

declare(strict_types=1);

namespace PackageInfo;

use Psr\Container\ContainerInterface;

use function file_exists;
use function file_get_contents;

class PackageContainerFactory
{
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
