<?php

declare(strict_types=1);

namespace PackageInfo\Information\Repository;

use JsonException;

use function file_get_contents;
use function json_decode;

use const JSON_THROW_ON_ERROR;

final class ComposerDetails
{
    public string $composerPackageName = '';

    public bool $composerJsonPresent = false;

    public string $phpVersion = 'Undefined';

    public array $requirements = [];

    public array $developmentRequirements = [];

    public function __construct(string $composerJsonPath)
    {
        $composerJsonPresent = false;
        $composerJson        = @file_get_contents($composerJsonPath);
        if (false !== $composerJson) {
            $composerJsonPresent = true;
            try {
                $composer = json_decode($composerJson, true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $e) {
                $composer = [];
            }
        }

        $this->composerPackageName = $composer['name'] ?? '';

        $requirements = [];
        if (isset($composer['require'])) {
            foreach ($composer['require'] as $package => $versionConstraint) {
                $requirements[$package] = $versionConstraint;
            }
        }

        $this->requirements = $requirements;

        $developmentRequirements = [];
        if (isset($composer['require-dev'])) {
            foreach ($composer['require-dev'] as $package => $versionConstraint) {
                $developmentRequirements[$package] = $versionConstraint;
            }
        }

        $this->developmentRequirements = $developmentRequirements;
        $this->composerJsonPresent     = $composerJsonPresent;
    }

    public function isComposerJsonPresent(): bool
    {
        return $this->composerJsonPresent;
    }

    public function getPhpVersion(): string
    {
        return $this->phpVersion;
    }

    public function getRequirements(): array
    {
        return $this->requirements;
    }

    public function getVersionConstraintOfRequirement(string $package): string
    {
        return $this->getRequirements()[$package];
    }

    public function hasRequirement(string $package): bool
    {
        return isset($this->getRequirements()[$package]);
    }

    public function getDevelopmentRequirements(): array
    {
        return $this->developmentRequirements;
    }

    public function getVersionConstraintOfDevelopmentRequirement(string $package): string
    {
        return $this->getDevelopmentRequirements()[$package];
    }

    public function hasDevelopmentRequirement(string $package): bool
    {
        return isset($this->getDevelopmentRequirements()[$package]);
    }
}
