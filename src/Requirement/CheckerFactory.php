<?php

declare(strict_types=1);

namespace PackageInfo\Requirement;

use Psr\Container\ContainerInterface;

class CheckerFactory
{
    public function __invoke(ContainerInterface $container): Checker
    {
        $config = $container->get('config');

        return new Checker(
            $container->get(Checker::class),
            $config['requirements'],
            $config['development_requirements']
        );
    }
}
