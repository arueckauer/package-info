<?php

declare(strict_types=1);

namespace PackageInfo\PackageContainer;

use PackageInfo\PackageContainer;

use function file_exists;
use function file_get_contents;
use function file_put_contents;

class Cache
{
    private string $cacheFilePath;

    private PackageContainer $packageContainer;

    public function __construct(string $cacheFilePath)
    {
        $this->cacheFilePath    = $cacheFilePath;
        $this->packageContainer = new PackageContainer();

        $this->read();
    }

    public function __destruct()
    {
        $this->write();
    }

    public function getPackageContainer(): PackageContainer
    {
        return $this->packageContainer;
    }

    public function write(): void
    {
        file_put_contents(
            $this->cacheFilePath,
            $this->packageContainer->serialize()
        );
    }

    private function read(): void
    {
        if (! file_exists($this->cacheFilePath)) {
            return;
        }

        $this->packageContainer->unserialize(file_get_contents($this->cacheFilePath));
    }
}
