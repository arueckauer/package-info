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
    public function __construct(
        private readonly UrlComposer $urlComposer,
        private readonly FileReader $fileReader,
        private readonly MetaReader $reader,
    ) {
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

        $head = new Head(
            $this->reader->getPackageName(),
            Type::RELEASE,
            $release['tag_name'],
            $this->reader->isComposerJsonPresent(),
            $this->reader->getRequirements(),
            $this->reader->getDevelopmentRequirements(),
        );

        $package->addHead($head);
    }
}
