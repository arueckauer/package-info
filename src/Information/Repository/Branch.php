<?php

declare(strict_types=1);

namespace PackageInfo\Information\Repository;

use function sprintf;

class Branch
{
    private string $name;
    /** @var ComposerDetails */
    private $composerDetails;

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

        $this->composerDetails = new ComposerDetails($composerJsonPath);
        $this->setName($name);
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
        return $this->composerDetails->composerPackageName;
    }

    public function isComposerJsonPresent(): bool
    {
        return $this->composerDetails->composerJsonPresent;
    }

    public function getPhpVersion(): string
    {
        return $this->composerDetails->phpVersion;
    }

    public function getRequirements(): array
    {
        return $this->composerDetails->requirements;
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
        return $this->composerDetails->developmentRequirements;
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
