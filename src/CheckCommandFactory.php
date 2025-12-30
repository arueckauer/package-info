<?php

declare(strict_types=1);

namespace PackageInfo;

use PackageInfo\Requirement\Checker;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final readonly class CheckCommandFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): CheckCommand
    {
        return new CheckCommand(
            $container->get(PackageContainer::class),
            $container->get(Checker::class)
        );
    }
}
