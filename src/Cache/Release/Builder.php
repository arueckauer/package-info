<?php

declare(strict_types=1);

namespace PackageInfo\Cache\Release;

use PackageInfo\Package;
use PackageInfo\Repository\Head;
use PackageInfo\Repository\Head\Type;
use Symfony\Component\Console\Helper\ProgressBar;

final readonly class Builder
{
    public function __invoke(
        Package $package,
        array $release,
        array $composerData,
        ProgressBar $progressBarReleases,
    ): Package {
        $progressBarReleases->setMessage($release['tag_name']);
        $progressBarReleases->advance();

        $head = new Head(
            $composerData['name'] ?? '',
            Type::Release->value,
            $release['tag_name'],
            $composerData !== [],
            $composerData['require'] ?? [],
            $composerData['require-dev'] ?? [],
        );

        return $package->withHead($head);
    }
}
