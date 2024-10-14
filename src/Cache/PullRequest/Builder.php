<?php

declare(strict_types=1);

namespace PackageInfo\Cache\PullRequest;

use PackageInfo\Composer\Json\FileReader;
use PackageInfo\Composer\Json\MetaReader;
use PackageInfo\Composer\Json\UrlComposer;
use PackageInfo\Package;
use PackageInfo\Repository\Head;
use PackageInfo\Repository\Head\Type;
use Symfony\Component\Console\Helper\ProgressBar;

use function explode;

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
        array $pullRequest,
        ProgressBar $progressBarPullRequests,
    ): void {
        $progressBarPullRequests->setMessage($pullRequest['head']['repo']['full_name'] ?? '');
        $progressBarPullRequests->advance();

        $pullRequestExists = isset($pullRequest['head']['repo']['full_name'])
            && null !== $pullRequest['head']['repo']['full_name'];

        if (! $pullRequestExists) {
            return;
        }

        [$headOwner, $headRepository] = explode('/', (string) $pullRequest['head']['repo']['full_name']);

        $url = ($this->urlComposer)($headOwner, $headRepository, $pullRequest['head']['ref']);
        $this->reader->setComposer(($this->fileReader)($url));

        $head = new Head(
            $this->reader->getPackageName(),
            Type::PULL_REQUEST,
            $pullRequest['head']['ref'],
            $this->reader->isComposerJsonPresent(),
            $this->reader->getRequirements(),
            $this->reader->getDevelopmentRequirements(),
        );

        $package->addHead($head);
    }
}
