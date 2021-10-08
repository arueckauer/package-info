<?php

declare(strict_types=1);

namespace PackageInfo\Cache\PullRequest;

use PackageInfo\Composer\Json\FileReader;
use PackageInfo\Composer\Json\MetaReader;
use PackageInfo\Composer\Json\UrlComposer;
use Psr\Container\ContainerInterface;

class BuilderFactory
{
    public function __invoke(ContainerInterface $container): Builder
    {
        return new Builder(
            $container->get(UrlComposer::class),
            $container->get(FileReader::class),
            $container->get(MetaReader::class)
        );
    }
}
