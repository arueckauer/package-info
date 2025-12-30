<?php

declare(strict_types=1);

namespace PackageInfo\Requirement;

use PackageInfo\Requirement\Version\Checker as VersionChecker;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class CheckerFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): Checker
    {
        return new Checker(
            $container->get(VersionChecker::class)
        );
    }
}
