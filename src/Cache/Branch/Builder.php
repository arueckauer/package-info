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

final readonly class Builder
{
    /**
     * @param string[] $ignoreBranchNames
     */
    public function __construct(
        private array $ignoreBranchNames,
        private UrlComposer $urlComposer,
        private FileReader $fileReader,
        private MetaReader $reader,
    ) {
    }

    public function __invoke(
        Package $package,
        array $branch,
        ProgressBar $progressBarBranches
    ): Package {
            $progressBarBranches->setMessage($branch['name']);
            $progressBarBranches->advance();

        if (in_array($branch['name'], $this->ignoreBranchNames, true)) {
            return $package;
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

        return $package->withHead($head);
    }
}
