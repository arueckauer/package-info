<?php

declare(strict_types=1);

namespace PackageInfo\Requirement\Version;

use Composer\Semver\VersionParser;
use Psr\Container\ContainerInterface;

class CheckFactory
{
    public function __invoke(ContainerInterface $container): Check
    {
        return new Check(
            new VersionParser()
        );
    }
}
