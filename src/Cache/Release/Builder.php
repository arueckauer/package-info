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

final readonly class Builder
{
    public function __construct(
        private UrlComposer $urlComposer,
        private FileReader $fileReader,
        private MetaReader $reader,
    ) {
    }

    public function __invoke(
        Package $package,
        array $release,
        ProgressBar $progressBarReleases
    ): Package {
        $progressBarReleases->setMessage($release['tag_name']);
        $progressBarReleases->advance();

        $url = ($this->urlComposer)($package->organization, $package->repository, $release['tag_name']);
        $this->reader->setComposer(($this->fileReader)($url));

        $head = new Head(
            $this->reader->getPackageName(),
            Type::Release->value,
            $release['tag_name'],
            $this->reader->isComposerJsonPresent(),
            $this->reader->getRequirements(),
            $this->reader->getDevelopmentRequirements(),
        );

        return $package->withHead($head);
    }
}
