<?php

declare(strict_types=1);

namespace PackageInfo\Composer\Json;

use function count;

class MetaReader
{
    private array $composer = [];

    public function setComposer(array $composer): void
    {
        $this->composer = $composer;
    }

    public function getPackageName(): string
    {
        return $this->composer['name'] ?? '';
    }

    public function isComposerJsonPresent(): bool
    {
        return count($this->composer) > 0;
    }

    public function getRequirements(): array
    {
        $requirements = [];
        if (isset($this->composer['require'])) {
            foreach ($this->composer['require'] as $package => $versionConstraint) {
                $requirements[$package] = $versionConstraint;
            }
        }

        return $requirements;
    }

    public function getDevelopmentRequirements(): array
    {
        $developmentRequirements = [];
        if (isset($this->composer['require-dev'])) {
            foreach ($this->composer['require-dev'] as $package => $versionConstraint) {
                $developmentRequirements[$package] = $versionConstraint;
            }
        }

        return $developmentRequirements;
    }
}
