<?php

declare(strict_types=1);

namespace PackageInfo\Composer\Json;

use Psr\Container\ContainerInterface;

final readonly class MetaReaderFactory
{
    public function __invoke(ContainerInterface $container): MetaReader
    {
        return new MetaReader();
    }
}
