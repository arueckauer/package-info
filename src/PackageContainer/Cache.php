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
        private string $cacheFilePath,
        private JsonSerializer $serializer,
    ) {}

    /**
     * @throws CacheFileNotWritable
     * @mago-expect lint:no-error-control-operator
     */
    public function write(string $organization, PackageContainer $packageContainer): void
    {
        $result = @file_put_contents(
            $this->cacheFilePath,
            $this->serializer->serialize($packageContainer, $organization),
        );

        if (false === $result) {
            throw CacheFileNotWritable::fromFilename($this->cacheFilePath);
        }
    }

    public function read(): PackageContainer
    {
        if (!file_exists($this->cacheFilePath)) {
            return new PackageContainer();
        }

        $cacheContent = file_get_contents($this->cacheFilePath);

        if ($cacheContent === false || $cacheContent === '') {
            return new PackageContainer();
        }

        return $this->serializer->deserialize($cacheContent);
    }
}
