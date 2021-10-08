<?php

declare(strict_types=1);

namespace PackageInfo\Composer\Json;

use Psr\Container\ContainerInterface;

class FileReaderFactory
{
    public function __invoke(ContainerInterface $container): FileReader
    {
        return new FileReader();
    }
}
