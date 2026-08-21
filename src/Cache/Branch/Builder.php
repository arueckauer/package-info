<?php

declare(strict_types=1);

namespace PackageInfo\Cache\Branch;

use PackageInfo\Package;
use PackageInfo\Repository\Head;
use PackageInfo\Repository\Head\Type;
use Symfony\Component\Console\Helper\ProgressBar;

use function in_array;

final readonly class Builder
{
    /**
     * @param string[] $ignoreBranchNames
     */
    public function __construct(
        private array $ignoreBranchNames,
    ) {}

    public function __invoke(
        Package $package,
        array $branch,
        array $composerData,
        ProgressBar $progressBarBranches,
    ): Package {
        $progressBarBranches->setMessage($branch['name']);
        $progressBarBranches->advance();

        if (in_array($branch['name'], $this->ignoreBranchNames, true)) {
            return $package;
        }

        $head = new Head(
            $composerData['name'] ?? '',
            Type::Branch->value,
            $branch['name'],
            $composerData !== [],
            $composerData['require'] ?? [],
            $composerData['require-dev'] ?? [],
        );

        return $package->withHead($head);
    }
}
