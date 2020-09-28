<?php

declare(strict_types=1);

namespace PackageInfo\Command;

use Github\Client;
use Github\Exception\RuntimeException;
use PackageInfo\Information\Package;
use PackageInfo\Information\Repository\Branch;
use PackageInfo\Information\Repository\File;
use PackageInfo\Information\Repository\PullRequest;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function file_put_contents;
use function in_array;
use function serialize;
use function sprintf;

class CacheBuildCommand extends Command
{
    public const OPTION_WITH_PULL_REQUESTS       = 'with-pull-requests';
    public const OPTION_WITH_PULL_REQUESTS_SHORT = 'p';
    private const PULL_REQUEST_PARAMETERS        = [
        'state' => 'open',
    ];

    private Client $client;

    private array $organizations;

    private array $ignoreRepositories;

    private array $ignoreBranchNames;

    private string $cacheFilePath;

    public function __construct(
        Client $client,
        array $organizations,
        array $ignoreRepositories,
        array $ignoreBranchNames,
        string $cacheFilePath
    ) {
        $this->client             = $client;
        $this->organizations      = $organizations;
        $this->ignoreRepositories = $ignoreRepositories;
        $this->ignoreBranchNames  = $ignoreBranchNames;
        $this->cacheFilePath      = $cacheFilePath;

        parent::__construct();
    }

    public function configure(): void
    {
        $this->setName('cache:build')
            ->setDescription('Caches package information for repositories of configured organization')
            ->addOption(
                self::OPTION_WITH_PULL_REQUESTS,
                self::OPTION_WITH_PULL_REQUESTS_SHORT,
                null,
                'Receives PRs aswell'
            );
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $packages = [];

        foreach ($this->organizations as $organization) {
            $output->writeln(sprintf(
                '<comment>Retrieving repositories for <info>%s</info> organization</comment>',
                $organization
            ));

            $repositories = $this->repositories($organization);

            $progressBar = new ProgressBar($output);

            foreach ($progressBar->iterate($repositories) as $key => $repository) {
                $package = new Package(
                    $organization,
                    $repository
                );

                if (in_array($package->toString(), $this->ignoreRepositories, true)) {
                    continue;
                }

                $branches = $this->branches(
                    $organization,
                    $repository
                );
                $package->setBranches($branches);

                if ($input->getOption(self::OPTION_WITH_PULL_REQUESTS)) {
                    $pullRequests = $this->pullRequests($organization, $repository);
                    $package->setPullRequests($pullRequests);
                }

                $packages[] = $package;
            }

            $output->writeln('');
        }

        file_put_contents($this->cacheFilePath, serialize($packages));

        return 0;
    }

    private function repositories(string $org): array
    {
        $repositories = [];
        $page         = 1;
        while (true) {
            $repos = $this->client->organization()->repositories($org, 'all', $page);
            ++$page;

            if (! $repos) {
                break;
            }

            foreach ($repos as $repo) {
                $repositories[] = $repo['name'];
            }
        }

        return $repositories;
    }

    /**
     * @psalm-return list<Branch>
     */
    private function branches(string $organization, string $repository): array
    {
        $branches = [];
        foreach ($this->client->repo()->branches($organization, $repository) as $branchArray) {
            if (in_array($branchArray['name'], $this->ignoreBranchNames, true)) {
                continue;
            }

            $branches[] = new Branch($organization, $repository, $branchArray['name']);
        }

        return $branches;
    }

    /**
     * @psalm-return list<PullRequest>
     */
    private function pullRequests(string $organization, string $repository): array
    {
        $pullRequestsFromGithub = $this->client
            ->pullRequests()
            ->all($organization, $repository, self::PULL_REQUEST_PARAMETERS);

        $pullRequests = [];
        foreach ($pullRequestsFromGithub as $pullRequest) {
            $instance = new PullRequest(
                $organization,
                $repository,
                $pullRequest['number'],
                $pullRequest['head']['label']
            );

            try {
                $filesFromPullRequest = $this->client->pullRequests()->files(
                    $instance->organization,
                    $instance->repository,
                    $instance->number
                );
            } catch (RuntimeException $exception) {
                $filesFromPullRequest = [];
            }

            $files = [];
            foreach ($filesFromPullRequest as $fileFromPullRequest) {
                $files[] = new File($fileFromPullRequest['filename'], $fileFromPullRequest['raw_url']);
            }

            $instance = $instance->withFiles($files);

            if (! $instance->hasComposerChanges()) {
                continue;
            }

            $instance->resolveComposerDetails();

            $pullRequests[] = $instance;
        }

        return $pullRequests;
    }
}
