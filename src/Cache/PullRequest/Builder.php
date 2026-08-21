<?php

declare(strict_types=1);

namespace PackageInfo\Cache\PullRequest;

use PackageInfo\Package;
use PackageInfo\Repository\Head;
use PackageInfo\Repository\Head\Type;
use Symfony\Component\Console\Helper\ProgressBar;

final readonly class Builder
{
    public function __invoke(
        Package $package,
        array $pullRequest,
        array $composerData,
        ProgressBar $progressBarPullRequests,
    ): Package {
        $progressBarPullRequests->setMessage($pullRequest['head']['repo']['full_name'] ?? '');
        $progressBarPullRequests->advance();

        if (($pullRequest['head']['repo']['full_name'] ?? null) === null) {
            return $package;
        }

        $head = new Head(
            $composerData['name'] ?? '',
            Type::PullRequest->value,
            $pullRequest['head']['ref'],
            $composerData !== [],
            $composerData['require'] ?? [],
            $composerData['require-dev'] ?? [],
        );

        return $package->withHead($head);
    }
}
