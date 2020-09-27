<?php

declare(strict_types=1);

namespace PackageInfo\Information;

use Composer\Semver\Comparator;
use Composer\Semver\VersionParser;
use PackageInfo\Information\Repository\ComposerDetails;

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

    public function parseRequirements(ComposerDetails $composerDetails): array
    {
        $requirements = [];

        foreach ($this->requirements as $requiredPackage => $lowestRequiredVersion) {
            $requirements[] = $this->parse(
                $composerDetails,
                $requiredPackage,
                $lowestRequiredVersion
            );
        }

        return $requirements;
    }

    public function parseDevelopmentRequirements(ComposerDetails $composerDetails): array
    {
        $requirements = [];

        foreach ($this->developmentRequirements as $requiredPackage => $lowestRequiredVersion) {
            $requirements[] = $this->parse(
                $composerDetails,
                $requiredPackage,
                $lowestRequiredVersion,
                true
            );
        }

        return $requirements;
    }

    private function parse(
        ComposerDetails $composerDetails,
        string $package,
        string $minimumVersion,
        bool $development = false
    ): string {
        if (! $this->hasRequirement($composerDetails, $package, $development)) {
            return sprintf(
                '%s: <error>n/a</error>',
                $package
            );
        }

        $versionConstraint = $this->versionConstraint(
            $composerDetails,
            $package,
            $development
        );

        $constraint    = $this->versionParser->parseConstraints($versionConstraint);
        $isSupported   = $constraint->matches($this->versionParser->parseConstraints($minimumVersion));
        $versionFormat = $isSupported === true ? 'info' : 'comment';

        return sprintf(
            '%1$s: <%3$s>%2$s</%3$s>',
            $package,
            $versionConstraint,
            $versionFormat
        );
    }

    private function hasRequirement(ComposerDetails $composerDetails, string $package, bool $development): bool
    {
        if (false === $development) {
            return $composerDetails->hasRequirement($package);
        }

        return $composerDetails->hasDevelopmentRequirement($package);
    }

    private function versionConstraint(ComposerDetails $composerDetails, string $package, bool $development): string
    {
        if (false === $development) {
            return $composerDetails->getVersionConstraintOfRequirement($package);
        }

        return $composerDetails->getVersionConstraintOfDevelopmentRequirement($package);
    }
}
