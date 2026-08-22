<?php

declare(strict_types=1);

namespace PackageInfo;

use PackageInfo\PackageContainer\JsonSerializer;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function assert;
use function file_get_contents;
use function glob;
use function is_array;
use function rtrim;
use function sprintf;

final readonly class PackageContainerFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): PackageContainer
    {
        $serializer = $container->get(JsonSerializer::class);
        assert($serializer instanceof JsonSerializer);
        $config = $container->get('config');
        $cacheDirectory = rtrim((string) ($config['cache_directory'] ?? ''), '/\\');

        $files = glob(sprintf('%s/*.json', $cacheDirectory));
        assert(is_array($files));

        $containers = [];
        foreach ($files as $file) {
            $content = file_get_contents($file);

            if ($content === false || $content === '') {
                continue;
            }

            $containers[] = $serializer->deserialize($content);
        }

        return PackageContainer::merge(...$containers);
    }
}
