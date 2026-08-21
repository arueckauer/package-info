<?php

declare(strict_types=1);

namespace PackageInfo\Cache\Branch;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final readonly class BuilderFactory
{
    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container): Builder
    {
        $config = $container->get('config');

        return new Builder($config['ignore_branches']);
    }
}
