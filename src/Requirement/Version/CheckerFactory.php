<?php

declare(strict_types=1);

namespace PackageInfo\Requirement\Version;

use Composer\Semver\VersionParser;

final class CheckerFactory
{
    public function __invoke(): Checker
    {
        return new Checker(new VersionParser());
    }
}
