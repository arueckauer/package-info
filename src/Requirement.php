<?php

declare(strict_types=1);

namespace PackageInfo;

use PackageInfo\Repository\Head;
use PackageInfo\Requirement\Version\Check;

use function sprintf;

class Requirement
{
    private Check $check;
    private array $requirements;
    private array $developmentRequirements;

    public function __construct(
        Check $check,
        array $requirements,
        array $developmentRequirements
    ) {
        $this->check                   = $check;
        $this->requirements            = $requirements;
        $this->developmentRequirements = $developmentRequirements;
    }

    public function parseRequirements(Head $head): array
    {
        $requirements = [];

        foreach ($this->requirements as $requiredPackage => $lowestRequiredVersion) {
            $requirements[] = $this->parseRequirement(
                $head,
                $requiredPackage,
                $lowestRequiredVersion
            );
        }

        return $requirements;
    }

    public function parseDevelopmentRequirements(Head $head): array
    {
        $requirements = [];

        foreach ($this->developmentRequirements as $requiredPackage => $lowestRequiredVersion) {
            $requirements[] = $this->parseDevelopmentRequirement(
                $head,
                $requiredPackage,
                $lowestRequiredVersion
            );
        }

        return $requirements;
    }

    private function parseRequirement(
        Head $head,
        string $package,
        string $minimumVersion
    ): string {
        if (! $head->hasRequirement($package)) {
            return sprintf(
                '%s: <error>n/a</error>',
                $package
            );
        }

        $versionConstraint = $head->getVersionConstraintOfRequirement($package);
        $isSupported       = ($this->check)($minimumVersion, $versionConstraint);
        $versionFormat     = $isSupported === true ? 'info' : 'comment';

        return sprintf(
            '%1$s: <%3$s>%2$s</%3$s>',
            $package,
            $versionConstraint,
            $versionFormat
        );
    }

    private function parseDevelopmentRequirement(
        Head $head,
        string $package,
        string $minimumVersion
    ): string {
        if (! $head->hasDevelopmentRequirement($package)) {
            return sprintf(
                '%s: <error>n/a</error>',
                $package
            );
        }

        $versionConstraint = $head->getVersionConstraintOfDevelopmentRequirement($package);
        $isSupported       = ($this->check)($minimumVersion, $versionConstraint);
        $versionFormat     = $isSupported === true ? 'info' : 'comment';

        return sprintf(
            '%1$s: <%3$s>%2$s</%3$s>',
            $package,
            $versionConstraint,
            $versionFormat
        );
    }
}
