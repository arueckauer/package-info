<?php

declare(strict_types=1);

namespace PackageInfo\Cache\Release;

use PackageInfo\Composer\Json\FileReader;
use PackageInfo\Composer\Json\MetaReader;
use PackageInfo\Composer\Json\UrlComposer;
use Psr\Container\ContainerInterface;

class BuilderFactory
{
    public function __invoke(ContainerInterface $container): Builder
    {
        return new Builder(
            new UrlComposer(),
            new FileReader(),
            new MetaReader()
        );
    }
}
