<?php

declare(strict_types=1);

namespace PackageInfo\Command;

use PackageInfo\Cache\Builder;
use Psr\Container\ContainerInterface;

class CacheBuildCommandFactory
{
    public function __invoke(ContainerInterface $container): CacheBuildCommand
    {
        $config = $container->get('config');

        return new CacheBuildCommand(
            $config['organizations'],
            $container->get(Builder::class)
        );
    }
}
