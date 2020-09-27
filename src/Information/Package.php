<?php

declare(strict_types=1);

namespace PackageInfo\Information;

use PackageInfo\Information\Repository\Branch;
use PackageInfo\Information\Repository\PullRequest;

use function sprintf;

class Package
{
    private string $username;

    private string $repository;

    /** @var Branch[] */
    private array $branches;

    /** @var PullRequest[] */
    private array $pullRequests;

    public function __construct(
        string $username,
        string $repository,
        array $branches = [],
        array $pullRequests = []
    ) {
        $this->setUsername($username);
        $this->setRepository($repository);
        $this->setBranches($branches);
        $this->setPullRequests($pullRequests);
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

    public function addBranch(Branch $branch): void
    {
        $this->branches[] = $branch;
    }

    /**
     * @psalm-param list<PullRequest> $pullRequests
     */
    public function setPullRequests(array $pullRequests): void
    {
        $this->pullRequests = $pullRequests;
    }

    public function setBranches(array $branches): void
    {
        $this->branches = $branches;
    }

    /**
     * @return PullRequest[]
     */
    public function getPullRequests(): array
    {
        return $this->pullRequests;
    }
}
