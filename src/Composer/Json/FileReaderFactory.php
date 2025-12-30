<?php

declare(strict_types=1);

namespace PackageInfo\Composer\Json;

use Psr\Container\ContainerInterface;

final readonly class FileReaderFactory
{
    public function __invoke(ContainerInterface $container): FileReader
    {
        return new FileReader();
    }
}
