<?php

declare(strict_types=1);

namespace PackageInfo\Information;

use Composer\Semver\Comparator;
use Composer\Semver\VersionParser;
use PackageInfo\Information\Repository\Branch;

use function sprintf;

class Requirement
{
    private VersionParser $versionParser;

    private array $requirements;

    private array $developmentRequirements;

    public function __construct(VersionParser $versionParser, array $requirements, array $developmentRequirements)
    {
        $this->versionParser           = $versionParser;
        $this->requirements            = $requirements;
        $this->developmentRequirements = $developmentRequirements;
    }

    public function parseRequirements(Branch $branch): array
    {
        $requirements = [];

        foreach ($this->requirements as $requiredPackage => $lowestRequiredVersion) {
            $requirements[] = $this->parse(
                $branch,
                $requiredPackage,
                $lowestRequiredVersion
            );
        }

        return $requirements;
    }

    public function parseDevelopmentRequirements(Branch $branch): array
    {
        $requirements = [];

        foreach ($this->developmentRequirements as $requiredPackage => $lowestRequiredVersion) {
            $requirements[] = $this->parse(
                $branch,
                $requiredPackage,
                $lowestRequiredVersion,
                true
            );
        }

        return $requirements;
    }

    private function parse(
        Branch $branch,
        string $package,
        string $minimumVersion,
        bool $development = false
    ): string {
        if (false === $development) {
            $hasRequirementMethod    = 'hasRequirement';
            $versionConstraintGetter = 'getVersionConstraintOfRequirement';
        } else {
            $hasRequirementMethod    = 'hasDevelopmentRequirement';
            $versionConstraintGetter = 'getVersionConstraintOfDevelopmentRequirement';
        }

        if (! $branch->$hasRequirementMethod($package)) {
            return sprintf(
                '%s: <error>n/a</error>',
                $package
            );
        }

        $versionConstraint = $branch->$versionConstraintGetter($package);
        $constraint        = $this->versionParser->parseConstraints($versionConstraint);
        $lowerVersion      = $constraint->getLowerBound()->getVersion();
        $isSupported       = Comparator::greaterThanOrEqualTo($lowerVersion, $minimumVersion);
        $versionFormat     = $isSupported === true ? 'info' : 'comment';

        return sprintf(
            '%1$s: <%3$s>%2$s</%3$s>',
            $package,
            $versionConstraint,
            $versionFormat
        );
    }
}
