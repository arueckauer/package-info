<?php

declare(strict_types=1);

namespace PackageInfo\Requirement;

use PackageInfo\Repository\Head;
use PackageInfo\Requirement\Check\Result;
use PackageInfo\Requirement\Version\Checker as VersionChecker;

class Checker
{
    private VersionChecker $checker;
    private array $requirements;
    private array $developmentRequirements;

    public function __construct(
        VersionChecker $checker,
        array $requirements,
        array $developmentRequirements
    ) {
        $this->checker                 = $checker;
        $this->requirements            = $requirements;
        $this->developmentRequirements = $developmentRequirements;
    }

    /**
     * @return Result[]
     */
    public function checkRequirements(Head $head): array
    {
        $requirements = [];

        foreach ($this->requirements as $requiredPackage => $lowestRequiredVersion) {
            $requirements[] = $this->checkRequirement(
                $head,
                $requiredPackage,
                $lowestRequiredVersion
            );
        }

        return $requirements;
    }

    /**
     * @return Result[]
     */
    public function checkDevelopmentRequirements(Head $head): array
    {
        $requirements = [];

        foreach ($this->developmentRequirements as $requiredPackage => $lowestRequiredVersion) {
            $requirements[] = $this->checkDevelopmentRequirement(
                $head,
                $requiredPackage,
                $lowestRequiredVersion
            );
        }

        return $requirements;
    }

    private function checkRequirement(
        Head $head,
        string $package,
        string $minimumVersion
    ): Result {
        $result                  = new Result();
        $result->requirementName = $package;

        if (! $head->hasRequirement($package)) {
            $result->hasRequirement = false;
            return $result;
        }

        $versionConstraint = $head->getVersionConstraintOfRequirement($package);
        $isSupported       = ($this->checker)($minimumVersion, $versionConstraint);

        $result->hasRequirement    = true;
        $result->versionConstraint = $versionConstraint;
        $result->isSupported       = $isSupported;

        return $result;
    }

    private function checkDevelopmentRequirement(
        Head $head,
        string $package,
        string $minimumVersion
    ): Result {
        $result                  = new Result();
        $result->requirementName = $package;
        if (! $head->hasDevelopmentRequirement($package)) {
            $result->hasRequirement = false;
            return $result;
        }

        $versionConstraint = $head->getVersionConstraintOfDevelopmentRequirement($package);
        $isSupported       = ($this->checker)($minimumVersion, $versionConstraint);

        $result->hasRequirement    = true;
        $result->versionConstraint = $versionConstraint;
        $result->isSupported       = $isSupported;

        return $result;
    }
}
