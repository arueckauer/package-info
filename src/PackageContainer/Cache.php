<?php

declare(strict_types=1);

namespace PackageInfo\PackageContainer;

use PackageInfo\PackageContainer;
use PackageInfo\PackageContainer\Exception\CacheFileNotWritable;

use function file_exists;
use function file_get_contents;
use function file_put_contents;

final readonly class Cache
{
    public function __construct(
        private string $cacheDirectory,
        private JsonSerializer $serializer,
    ) {}

    /**
     * @throws CacheFileNotWritable
     * @mago-expect lint:no-error-control-operator
     */
    public function write(string $organization, PackageContainer $packageContainer): void
    {
        $cacheFilePath = $this->cacheFilePath($organization);

        $result = @file_put_contents(
            $cacheFilePath,
            $this->serializer->serialize($packageContainer, $organization),
        );

        if (false === $result) {
            throw CacheFileNotWritable::fromFilename($cacheFilePath);
        }
    }

    public function read(string $organization): PackageContainer
    {
        $cacheFilePath = $this->cacheFilePath($organization);

        if (!file_exists($cacheFilePath)) {
            return new PackageContainer();
        }

        $cacheContent = file_get_contents($cacheFilePath);

        if ($cacheContent === false || $cacheContent === '') {
            return new PackageContainer();
        }

        return $this->serializer->deserialize($cacheContent);
    }

    private function cacheFilePath(string $organization): string
    {
        return sprintf('%s/%s.json', $this->cacheDirectory, $organization);
    }
}
