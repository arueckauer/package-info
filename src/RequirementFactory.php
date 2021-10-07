<?php

declare(strict_types=1);

namespace PackageInfo;

use PackageInfo\Requirement\Version\Check;
use Psr\Container\ContainerInterface;

class RequirementFactory
{
    public function __invoke(ContainerInterface $container): Requirement
    {
        $config = $container->get('config');

        return new Requirement(
            $container->get(Check::class),
            $config['requirements'],
            $config['development_requirements']
        );
    }
}
