<?php

declare(strict_types=1);

namespace PackageInfo\Requirement;

use PackageInfo\Repository\Head;
use PackageInfo\Requirement\Check\Result;
use PackageInfo\Requirement\Version\Checker as VersionChecker;

final class Checker
{
    public array $requirements;
    public array $developmentRequirements;

    public function __construct(
        private readonly VersionChecker $checker,
    ) {
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
        if (! $head->hasRequirement($package)) {
            return new Result(
                $package,
                null,
                false,
                false
            );
        }

        $versionConstraint = $head->getVersionConstraintOfRequirement($package);
        $isSupported       = ($this->checker)($minimumVersion, $versionConstraint);

        return new Result(
            $package,
            $versionConstraint,
            true,
            $isSupported
        );
    }

    private function checkDevelopmentRequirement(
        Head $head,
        string $package,
        string $minimumVersion
    ): Result {
        if (! $head->hasDevelopmentRequirement($package)) {
            return new Result(
                $package,
                null,
                false,
                false
            );
        }

        $versionConstraint = $head->getVersionConstraintOfDevelopmentRequirement($package);
        $isSupported       = ($this->checker)($minimumVersion, $versionConstraint);

        return new Result(
            $package,
            $versionConstraint,
            true,
            $isSupported
        );
    }
}
