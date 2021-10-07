<?php

declare(strict_types=1);

namespace PackageInfo\Requirement\Version;

use Composer\Semver\Comparator;
use Composer\Semver\VersionParser;

class Checker
{
    private VersionParser $versionParser;

    public function __construct(VersionParser $versionParser)
    {
        $this->versionParser = $versionParser;
    }

    public function __invoke(string $minimumVersion, string $constraints): bool
    {
        $constraint   = $this->versionParser->parseConstraints($constraints);
        $lowerVersion = $constraint->getLowerBound()->getVersion();

        return Comparator::greaterThanOrEqualTo($lowerVersion, $minimumVersion);
    }
}
