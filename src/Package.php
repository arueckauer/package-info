<?php

declare(strict_types=1);

namespace PackageInfo;

use PackageInfo\Repository\Head;

use function sprintf;

class Package
{
    public string $organization;

    public string $repository;

    /** @var Head[] */
    private array $heads;

    public function __construct(
        string $username,
        string $repository,
        Head ...$heads
    ) {
        $this->organization = $username;
        $this->repository   = $repository;
        $this->heads        = $heads;
    }

    public function toString(): string
    {
        return sprintf(
            '%s/%s',
            $this->organization,
            $this->repository
        );
    }

    public function addHead(Head $head): void
    {
        $this->heads[] = $head;
    }

    public function getHeads(): array
    {
        return $this->heads;
    }
}
