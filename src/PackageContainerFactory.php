<?php

declare(strict_types=1);

namespace PackageInfo;

use PackageInfo\PackageContainer\JsonSerializer;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

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
        $packageContainer = new PackageContainer();
        $serializer = new JsonSerializer();

        $config = $container->get('config');
        $cacheDirectory = rtrim((string) ($config['cache_directory'] ?? ''), '/\\');

        $files = glob(sprintf('%s/*.json', $cacheDirectory));
        assert(is_array($files));

        foreach ($files as $file) {
            $content = file_get_contents($file);

            if ($content === false || $content === '') {
                continue;
            }

            $loaded = $serializer->deserialize($content);
            foreach ($loaded->all() as $package) {
                $packageContainer->add($package);
            }
        }

        return $packageContainer;
    }
}
