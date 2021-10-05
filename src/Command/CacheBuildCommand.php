<?php

declare(strict_types=1);

namespace PackageInfo\Command;

use Github\Client;
use PackageInfo\Information\Package;
use PackageInfo\Information\Repository\Branch;
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
            ->setDescription('Caches package information for repositories of configured organization');
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

                foreach ($this->client->repo()->branches($organization, $repository) as $branchArray) {
                    if (in_array($branchArray['name'], $this->ignoreBranchNames, true)) {
                        continue;
                    }

                    $branch = new Branch($organization, $repository, $branchArray['name']);
                    $package->addBranch($branch);
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
}
