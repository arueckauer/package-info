<?php

declare(strict_types=1);

namespace PackageInfo\PackageContainer;

use Exception;
use PackageInfo\PackageContainer;
use PackageInfo\PackageContainer\Exception\CacheFileNotWritable;

use function assert;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function is_string;

final readonly class Cache
{
    private JsonSerializer $serializer;

    public function __construct(
        private PackageContainer $packageContainer,
        private string $cacheFilePath,
        private string $organization,
    ) {
        $this->serializer = new JsonSerializer();
        $this->read();
    }

    /**
     * @throws Exception
     */
    public function __destruct()
    {
        $this->write();
    }

    public function getPackageContainer(): PackageContainer
    {
        return $this->packageContainer;
    }

    /**
     * @throws Exception
     * @mago-expect lint:no-error-control-operator
     */
    public function write(): void
    {
        $result = @file_put_contents(
            $this->cacheFilePath,
            $this->serializer->serialize($this->packageContainer, $this->organization),
        );

        if (false === $result) {
            throw CacheFileNotWritable::fromFilename($this->cacheFilePath);
        }
    }

    private function read(): void
    {
        if (!file_exists($this->cacheFilePath)) {
            return;
        }

        $cacheContent = file_get_contents($this->cacheFilePath);
        assert(is_string($cacheContent));

        if ($cacheContent === '') {
            return;
        }

        $loaded = $this->serializer->deserialize($cacheContent);
        foreach ($loaded->all() as $package) {
            $this->packageContainer->add($package);
        }
    }
}
