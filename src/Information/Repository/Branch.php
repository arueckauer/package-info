<?php

declare(strict_types=1);

namespace PackageInfo\Information\Repository;

use JsonException;

use function file_get_contents;
use function json_decode;
use function sprintf;

use const JSON_THROW_ON_ERROR;

class Branch
{
    private string $name;
    private string $composerPackageName    = '';
    private bool $composerJsonPresent      = false;
    private string $phpVersion             = 'Undefined';
    private array $requirements            = [];
    private array $developmentRequirements = [];

    public function __construct(
        string $username,
        string $repository,
        string $name
    ) {
        $composerJsonPath = sprintf(
            'https://raw.githubusercontent.com/%s/%s/%s/composer.json',
            $username,
            $repository,
            $name
        );

        $composerJsonPresent = false;
        $composerJson        = @file_get_contents($composerJsonPath);
        if (false !== $composerJson) {
            $composerJsonPresent = true;
            try {
                $composer = json_decode($composerJson, true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $e) {
            }
        }

        if (isset($composer['name'])) {
            $this->setComposerPackageName($composer['name']);
        }

        $requirements = [];
        if (isset($composer['require'])) {
            foreach ($composer['require'] as $package => $versionConstraint) {
                $requirements[$package] = $versionConstraint;
            }
        }

        $developmentRequirements = [];
        if (isset($composer['require-dev'])) {
            foreach ($composer['require-dev'] as $package => $versionConstraint) {
                $developmentRequirements[$package] = $versionConstraint;
            }
        }

        $this->setName($name);
        $this->setComposerJsonPresent($composerJsonPresent);
        $this->setPhpVersion($composer['require']['php'] ?? 'Undefined');
        $this->setRequirements($requirements);
        $this->setDevelopmentRequirements($developmentRequirements);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getComposerPackageName(): string
    {
        return $this->composerPackageName;
    }

    public function setComposerPackageName(string $composerPackageName): void
    {
        $this->composerPackageName = $composerPackageName;
    }

    public function isComposerJsonPresent(): bool
    {
        return $this->composerJsonPresent;
    }

    public function setComposerJsonPresent(bool $composerJsonPresent): void
    {
        $this->composerJsonPresent = $composerJsonPresent;
    }

    public function getPhpVersion(): string
    {
        return $this->phpVersion;
    }

    public function setPhpVersion(string $phpVersion): void
    {
        $this->phpVersion = $phpVersion;
    }

    public function getRequirements(): array
    {
        return $this->requirements;
    }

    public function getVersionConstraintOfRequirement(string $package): string
    {
        return $this->requirements[$package];
    }

    public function addRequirement(string $package, string $versionConstraint): void
    {
        $this->requirements[$package] = $versionConstraint;
    }

    public function hasRequirement(string $package): bool
    {
        return isset($this->getRequirements()[$package]);
    }

    public function setRequirements(array $requirements): void
    {
        $this->requirements = $requirements;
    }

    public function getDevelopmentRequirements(): array
    {
        return $this->developmentRequirements;
    }

    public function getVersionConstraintOfDevelopmentRequirement(string $package): string
    {
        return $this->developmentRequirements[$package];
    }

    public function addDevelopmentRequirement(string $package, string $versionConstraint): void
    {
        $this->developmentRequirements[$package] = $versionConstraint;
    }

    public function hasDevelopmentRequirement(string $package): bool
    {
        return isset($this->getDevelopmentRequirements()[$package]);
    }

    public function setDevelopmentRequirements(array $developmentRequirements): void
    {
        $this->developmentRequirements = $developmentRequirements;
    }
}
