<?php

declare(strict_types=1);

namespace PackageInfo\Requirement\Check;

readonly class Result
{
    public function __construct(
        public string $requirementName,
        public ?string $versionConstraint,
        public bool $hasRequirement,
        public bool $isSupported,
    ) {
    }
}
