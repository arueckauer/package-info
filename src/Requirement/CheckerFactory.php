<?php

declare(strict_types=1);

namespace PackageInfo\Requirement;

use PackageInfo\Requirement\Version\Checker as VersionChecker;
use Psr\Container\ContainerInterface;

class CheckerFactory
{
    public function __invoke(ContainerInterface $container): Checker
    {
        $config = $container->get('config');

        return new Checker(
            $container->get(VersionChecker::class),
            $config['requirements'],
            $config['development_requirements']
        );
    }
}
