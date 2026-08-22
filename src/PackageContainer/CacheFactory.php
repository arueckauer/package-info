<?php

declare(strict_types=1);

namespace PackageInfo\PackageContainer;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final readonly class CacheFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): Cache
    {
        $config = $container->get('config');
        $cacheDirectory = rtrim((string) ($config['cache_directory'] ?? ''), '/\\');

        $serializer = $container->get(JsonSerializer::class);
        assert($serializer instanceof JsonSerializer);

        return new Cache(
            $cacheDirectory,
            $serializer,
        );
    }
}
