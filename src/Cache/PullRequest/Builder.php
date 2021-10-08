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
        array $pullRequest,
        ProgressBar $progressBarPullRequests
    ): void {
        $progressBarPullRequests->setMessage($pullRequest['head']['repo']['full_name'] ?? '');
        $progressBarPullRequests->advance();

        $pullRequestExists = isset($pullRequest['head']['repo']['full_name'])
            && null !== $pullRequest['head']['repo']['full_name'];

        if (! $pullRequestExists) {
            return;
        }

        [$headOwner, $headRepository] = explode('/', $pullRequest['head']['repo']['full_name']);

        $url = ($this->urlComposer)($headOwner, $headRepository, $pullRequest['head']['ref']);
        $this->reader->setComposer(($this->fileReader)($url));

        $head                          = new Head();
        $head->packageName             = $this->reader->getPackageName();
        $head->headType                = Type::PULL_REQUEST;
        $head->headName                = $pullRequest['head']['ref'];
        $head->composerJsonPresent     = $this->reader->isComposerJsonPresent();
        $head->requirements            = $this->reader->getRequirements();
        $head->developmentRequirements = $this->reader->getDevelopmentRequirements();

        $package->addHead($head);
    }
}
