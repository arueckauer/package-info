<?php

declare(strict_types=1);

namespace PackageInfo\Cache\Release;

use PackageInfo\Composer\Json\FileReader;
use PackageInfo\Composer\Json\MetaReader;
use PackageInfo\Composer\Json\UrlComposer;
use PackageInfo\Package;
use PackageInfo\Repository\Head;
use PackageInfo\Repository\Head\Type;
use Symfony\Component\Console\Helper\ProgressBar;

class Builder
{
    private UrlComposer $urlComposer;
    private FileReader $fileReader;
    private MetaReader $reader;

    public function __construct(
        UrlComposer $urlComposer,
        FileReader $fileReader,
        MetaReader $reader
    ) {
        $this->urlComposer = $urlComposer;
        $this->fileReader  = $fileReader;
        $this->reader      = $reader;
    }

    public function __invoke(
        Package $package,
        array $release,
        ProgressBar $progressBarReleases
    ): void {
        $progressBarReleases->setMessage($release['tag_name']);
        $progressBarReleases->advance();

        $url = ($this->urlComposer)($package->organization, $package->repository, $release['tag_name']);
        $this->reader->setComposer(($this->fileReader)($url));

        $head                          = new Head();
        $head->packageName             = $this->reader->getPackageName();
        $head->headType                = Type::RELEASE;
        $head->headName                = $release['tag_name'];
        $head->composerJsonPresent     = $this->reader->isComposerJsonPresent();
        $head->requirements            = $this->reader->getRequirements();
        $head->developmentRequirements = $this->reader->getDevelopmentRequirements();

        $package->addHead($head);
    }
}
