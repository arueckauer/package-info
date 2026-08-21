<?php

declare(strict_types=1);

namespace PackageInfo\Cache\PullRequest;

final readonly class BuilderFactory
{
    public function __invoke(): Builder
    {
        return new Builder();
    }
}
