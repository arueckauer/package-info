<?php

declare(strict_types=1);

namespace PackageInfo\PackageContainer;

use PackageInfo\PackageContainer;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function rtrim;
use function sprintf;

final class CacheFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, string $organization = ''): Cache
    {
        $config = $container->get('config');
        $cacheDirectory = rtrim((string) ($config['cache_directory'] ?? ''), '/\\');
        $cacheFilePath = sprintf('%s/%s.json', $cacheDirectory, $organization !== '' ? $organization : 'packages');

        return new Cache(new PackageContainer(), $cacheFilePath, $organization);
    }
}
