<?php

declare(strict_types=1);

namespace PackageInfo\Requirement\Check;

class Result
{
    public string $requirementName;
    public string $versionConstraint;
    public bool $hasRequirement;
    public bool $isSupported;
}
