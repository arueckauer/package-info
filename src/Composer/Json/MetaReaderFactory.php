<?php

declare(strict_types=1);

namespace PackageInfo\Composer\Json;

use Psr\Container\ContainerInterface;

class MetaReaderFactory
{
    public function __invoke(ContainerInterface $container): MetaReader
    {
        return new MetaReader();
    }
}
