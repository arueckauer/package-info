<?php

declare(strict_types=1);

namespace PackageInfo\Information;

use Composer\Semver\VersionParser;
use Psr\Container\ContainerInterface;

class RequirementFactory
{
    public function __invoke(ContainerInterface $container): Requirement
    {
        $config = $container->get('config');

        return new Requirement(
            new VersionParser(),
            $config['requirements'],
            $config['development_requirements']
        );
    }
}
