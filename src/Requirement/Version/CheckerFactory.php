<?php

declare(strict_types=1);

namespace PackageInfo\Requirement\Version;

use Composer\Semver\VersionParser;
use Psr\Container\ContainerInterface;

final class CheckerFactory
{
    public function __invoke(ContainerInterface $container): Checker
    {
        return new Checker(
            new VersionParser()
        );
    }
}
