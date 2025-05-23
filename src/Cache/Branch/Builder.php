<?php

declare(strict_types=1);

namespace PackageInfo\Cache\Branch;

use PackageInfo\Composer\Json\FileReader;
use PackageInfo\Composer\Json\MetaReader;
use PackageInfo\Composer\Json\UrlComposer;
use PackageInfo\Package;
use PackageInfo\Repository\Head;
use PackageInfo\Repository\Head\Type;
use Symfony\Component\Console\Helper\ProgressBar;

use function in_array;

class Builder
{
    /**
     * @param string[] $ignoreBranchNames
     */
    public function __construct(
        private readonly array $ignoreBranchNames,
        private readonly UrlComposer $urlComposer,
        private readonly FileReader $fileReader,
        private readonly MetaReader $reader,
    ) {
    }

    public function __invoke(
        Package $package,
        array $branch,
        ProgressBar $progressBarBranches
    ): void {
            $progressBarBranches->setMessage($branch['name']);
            $progressBarBranches->advance();

        if (in_array($branch['name'], $this->ignoreBranchNames, true)) {
            return;
        }

            $url = ($this->urlComposer)($package->organization, $package->repository, $branch['name']);
            $this->reader->setComposer(($this->fileReader)($url));

            $head = new Head(
                $this->reader->getPackageName(),
                Type::BRANCH,
                $branch['name'],
                $this->reader->isComposerJsonPresent(),
                $this->reader->getRequirements(),
                $this->reader->getDevelopmentRequirements(),
            );

            $package->addHead($head);
    }
}
