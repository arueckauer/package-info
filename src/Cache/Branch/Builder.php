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
    /** @var string[] */
    private array $ignoreBranchNames;
    private UrlComposer $urlComposer;
    private FileReader $fileReader;
    private MetaReader $reader;

    /**
     * @param string[] $ignoreBranchNames
     */
    public function __construct(
        array $ignoreBranchNames,
        UrlComposer $urlComposer,
        FileReader $fileReader,
        MetaReader $reader
    ) {
        $this->ignoreBranchNames = $ignoreBranchNames;
        $this->urlComposer       = $urlComposer;
        $this->fileReader        = $fileReader;
        $this->reader            = $reader;
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

            $head                          = new Head();
            $head->packageName             = $this->reader->getPackageName();
            $head->headType                = Type::BRANCH;
            $head->headName                = $branch['name'];
            $head->composerJsonPresent     = $this->reader->isComposerJsonPresent();
            $head->requirements            = $this->reader->getRequirements();
            $head->developmentRequirements = $this->reader->getDevelopmentRequirements();

            $package->addHead($head);
    }
}
