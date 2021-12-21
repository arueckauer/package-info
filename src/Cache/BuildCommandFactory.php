<?php

declare(strict_types=1);

namespace PackageInfo\Cache;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class BuildCommandFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): BuildCommand
    {
        $config = $container->get('config');

        return new BuildCommand(
            $config['organizations'],
            $container->get(Builder::class)
        );
    }
}
