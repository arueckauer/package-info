<?php

declare(strict_types=1);

namespace PackageInfo\Cache;

use Psr\Container\ContainerInterface;

class BuildCommandFactory
{
    public function __invoke(ContainerInterface $container): BuildCommand
    {
        $config = $container->get('config');

        return new BuildCommand(
            $config['organizations'],
            $container->get(Builder::class)
        );
    }
}
