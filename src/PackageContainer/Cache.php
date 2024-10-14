<?php

declare(strict_types=1);

namespace PackageInfo\PackageContainer;

use Exception;
use PackageInfo\PackageContainer;
use PackageInfo\PackageContainer\Exception\CacheFileNotWritableException;

use function file_exists;
use function file_get_contents;
use function file_put_contents;

class Cache
{
    private readonly PackageContainer $packageContainer;

    public function __construct(
        private readonly string $cacheFilePath,
    ) {
        $this->packageContainer = new PackageContainer();

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
     */
    public function write(): void
    {
        $result = @file_put_contents(
            $this->cacheFilePath,
            (string) $this->packageContainer->serialize()
        );

        if (false === $result) {
            throw CacheFileNotWritableException::fromFilename($this->cacheFilePath);
        }
    }

    private function read(): void
    {
        if (! file_exists($this->cacheFilePath)) {
            return;
        }

        $this->packageContainer->unserialize(file_get_contents($this->cacheFilePath));
    }
}
