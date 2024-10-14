<?php

declare(strict_types=1);

namespace PackageInfo;

use PackageInfo\Repository\Head;

use function sprintf;

class Package
{
    /** @var Head[] */
    private array $heads;

    public function __construct(
        public string $organization,
        public string $repository,
        Head ...$heads
    ) {
        $this->heads = $heads;
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
