<?php

declare(strict_types=1);

namespace PackageInfo\Repository;

class Head
{
    public string $packageName            = '';
    public string $headType               = '';
    public string $headName               = '';
    public bool $composerJsonPresent      = false;
    public array $requirements            = [];
    public array $developmentRequirements = [];

    public function hasRequirement(string $package): bool
    {
        return isset($this->requirements[$package]);
    }

    public function getVersionConstraintOfRequirement(string $package): string
    {
        return $this->requirements[$package];
    }

    public function hasDevelopmentRequirement(string $package): bool
    {
        return isset($this->developmentRequirements[$package]);
    }

    public function getVersionConstraintOfDevelopmentRequirement(string $package): string
    {
        return $this->developmentRequirements[$package];
    }
}
