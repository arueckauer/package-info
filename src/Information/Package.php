<?php

declare(strict_types=1);

namespace PackageInfo\Information;

use PackageInfo\Information\Repository\Branch;

use function sprintf;

class Package
{
    private string $username;

    private string $repository;

    /** @var Branch[] */
    private array $branches;

    public function __construct(
        string $username,
        string $repository,
        array $branches = []
    ) {
        $this->setUsername($username);
        $this->setRepository($repository);
        $this->setBranches($branches);
    }

    public function toString(): string
    {
        return sprintf(
            '%s/%s',
            $this->getUsername(),
            $this->getRepository()
        );
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getRepository(): string
    {
        return $this->repository;
    }

    public function setRepository(string $repository): void
    {
        $this->repository = $repository;
    }

    public function getBranches(): array
    {
        return $this->branches;
    }

    public function getBranchesAsArray(): array
    {
        $branches = [];

        foreach ($this->getBranches() as $branch) {
            $branches[] = $branch->toArray();
        }

        return $branches;
    }

    public function addBranch(Branch $branch): void
    {
        $this->branches[] = $branch;
    }

    public function setBranches(array $branches): void
    {
        $this->branches = $branches;
    }
}
