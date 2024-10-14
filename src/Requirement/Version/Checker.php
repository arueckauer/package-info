<?php

declare(strict_types=1);

namespace PackageInfo\Requirement\Version;

use Composer\Semver\VersionParser;

class Checker
{
    public function __construct(
        private readonly VersionParser $versionParser,
    ) {
    }

    public function __invoke(string $minimumVersion, string $constraints): bool
    {
        $actualConstraint   = $this->versionParser->parseConstraints($constraints);
        $requiredConstraint = $this->versionParser->parseConstraints($minimumVersion);

        return $actualConstraint->matches($requiredConstraint);
    }
}
