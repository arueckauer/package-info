<?php

declare(strict_types=1);

namespace PackageInfo\Cache;

use Exception;
use Github\Client;
use PackageInfo\Cache\Branch\Builder as BranchBuilder;
use PackageInfo\Cache\PullRequest\Builder as PullRequestBuilder;
use PackageInfo\Cache\Release\Builder as ReleaseBuilder;
use PackageInfo\Composer\Json\BatchFetcher;
use PackageInfo\Composer\Json\UrlComposer;
use PackageInfo\Console\Helper\ProgressBar;
use PackageInfo\Package;
use PackageInfo\PackageContainer\Cache;
use Symfony\Component\Console\Helper\ProgressBar as SymfonyProgressBar;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\ConsoleSectionOutput;

use function count;
use function explode;
use function in_array;
use function sprintf;

final class Builder
{
    private ?ConsoleSectionOutput $sectionMain = null;
    private ?ConsoleSectionOutput $sectionHeads = null;

    public function __construct(
        private readonly Client $client,
        private readonly array $ignoreRepositories,
        private readonly Cache $cache,
        private readonly BranchBuilder $branchBuilder,
        private readonly ReleaseBuilder $releaseBuilder,
        private readonly PullRequestBuilder $pullRequestBuilder,
        private readonly BatchFetcher $batchFetcher,
        private readonly UrlComposer $urlComposer,
    ) {}

    /**
     * @throws Exception
     */
    public function __invoke(ConsoleOutputInterface $output, string $organization): void
    {
        if (null === $this->sectionMain || null === $this->sectionHeads) {
            $this->sectionMain = $output->section();
            $this->sectionHeads = $output->section();
        }

        $output->writeln(sprintf(
            '<comment>Retrieving repositories for <info>%s</info> organization</comment>',
            $organization,
        ));

        $packages = $this->repositoriesAsPackages($organization);

        $progressBar = new SymfonyProgressBar($this->sectionMain);
        $progressBar->setFormat(ProgressBar::FORMAT_MAIN);
        $progressBar->setMessage('');
        $progressBar->setMaxSteps(count($packages));

        foreach ($packages as $package) {
            $progressBar->setMessage($package->toString());
            $progressBar->advance();

            if (in_array($package->toString(), $this->ignoreRepositories, true)) {
                continue;
            }

            $branches = $this->client->repo()->branches($package->organization, $package->repository);
            $releases = $this->client->repo()->releases()->all($package->organization, $package->repository);
            $pullRequests = $this->client->pullRequests()->all($package->organization, $package->repository);

            $urls = [];
            foreach ($branches as $i => $branch) {
                $urls['b:' . $i] = ($this->urlComposer)($package->organization, $package->repository, $branch['name']);
            }
            foreach ($releases as $i => $release) {
                $urls['r:' . $i] = ($this->urlComposer)(
                    $package->organization,
                    $package->repository,
                    $release['tag_name'],
                );
            }
            foreach ($pullRequests as $i => $pullRequest) {
                $repoFullName = $pullRequest['head']['repo']['full_name'] ?? null;
                if ($repoFullName !== null) {
                    [$headOwner, $headRepository] = explode('/', (string) $repoFullName, 2);
                    $urls['p:' . $i] = ($this->urlComposer)($headOwner, $headRepository, $pullRequest['head']['ref']);
                }
            }

            $fetched = $this->batchFetcher->fetch($urls);

            if (count($branches) > 0) {
                $progressBarBranches = new SymfonyProgressBar($this->sectionHeads);
                $progressBarBranches->setFormat('format_branches');
                $progressBarBranches->setMaxSteps(count($branches));

                foreach ($branches as $i => $branch) {
                    $package = ($this->branchBuilder)(
                        $package,
                        $branch,
                        $fetched['b:' . $i] ?? [],
                        $progressBarBranches,
                    );
                }

                $this->sectionHeads->clear();
            }

            if (count($releases) > 0) {
                $progressBarReleases = new SymfonyProgressBar($this->sectionHeads);
                $progressBarReleases->setFormat('format_releases');
                $progressBarReleases->setMaxSteps(count($releases));

                foreach ($releases as $i => $release) {
                    $package = ($this->releaseBuilder)(
                        $package,
                        $release,
                        $fetched['r:' . $i] ?? [],
                        $progressBarReleases,
                    );
                }
                $this->sectionHeads->clear();
            }

            if (count($pullRequests) > 0) {
                $progressBarPullRequests = new SymfonyProgressBar($this->sectionHeads);
                $progressBarPullRequests->setFormat('format_pull_requests');
                $progressBarPullRequests->setMaxSteps(count($pullRequests));

                foreach ($pullRequests as $i => $pullRequest) {
                    $package = ($this->pullRequestBuilder)(
                        $package,
                        $pullRequest,
                        $fetched['p:' . $i] ?? [],
                        $progressBarPullRequests,
                    );
                }
                $this->sectionHeads->clear();
            }

            $this->cache->getPackageContainer()->add($package);
        }

        $progressBar->setMessage('');
        $progressBar->advance(-1);
        $progressBar->advance();
        $output->writeln('');

        $this->cache->write();
    }

    /**
     * @return array<Package>
     */
    public function repositoriesAsPackages(string $org): array
    {
        $packages = [];
        $page = 1;
        while (true) {
            $repos = $this->client->organization()->repositories($org, 'all', $page);
            ++$page;

            if (!$repos) {
                break;
            }

            foreach ($repos as $repo) {
                $packages[] = new Package($org, $repo['name'], $repo['archived']);
            }
        }

        return $packages;
    }
}
