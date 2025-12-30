<?php

declare(strict_types=1);

namespace PackageInfo;

use PackageInfo\Repository\Head;

use function sprintf;

final readonly class Package
{
    /** @var Head[] */
    public array $heads;

    public function __construct(
        public string $organization,
        public string $repository,
        public bool $isArchived,
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

    public function withHead(Head $head): self
    {
        $heads   = $this->heads;
        $heads[] = $head;

        return new self(
            $this->organization,
            $this->repository,
            $this->isArchived,
            ...$heads
        );
    }
}
