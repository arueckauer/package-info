<?php

declare(strict_types=1);

namespace PackageInfo\Command;

use PackageInfo\Command\Exception\CacheNotFoundException;
use PackageInfo\Information\Package;
use PackageInfo\Information\Requirement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function array_keys;
use function array_pop;
use function count;
use function file_exists;
use function file_get_contents;
use function implode;
use function sprintf;
use function unserialize;

class PackageInfoListCommand extends Command
{
    private string $cacheFilePath;

    private Requirement $requirement;

    public function __construct(string $cacheFilePath, Requirement $requirement)
    {
        $this->cacheFilePath = $cacheFilePath;
        $this->requirement   = $requirement;

        parent::__construct();
    }

    public function configure(): void
    {
        $this->setName('package-info:list')
            ->setDescription('List all package information');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        if (! file_exists($this->cacheFilePath)) {
            throw CacheNotFoundException::byFilename($this->cacheFilePath);
        }

        $cacheContent = file_get_contents($this->cacheFilePath);
        /*** @var Package[] $packages */
        $packages = unserialize($cacheContent, [false]);

        $output->writeln(sprintf(
            '<comment>Showing information for <info>%s</info> packages</comment>',
            count($packages)
        ));
        $rows = [];

        foreach ($packages as $key => $package) {
            foreach ($package->getBranches() as $branch) {
                $composerDetails         = $branch->composerDetails;
                $requirements            = $this->requirement->parseRequirements($composerDetails);
                $developmentRequirements = $this->requirement->parseDevelopmentRequirements($composerDetails);

                $rows[] = [
                    'package'                  => $package->toString(),
                    'composer-package-name'    => $composerDetails->composerPackageName,
                    'head'                     => $branch->getName(),
                    'requirements'             => implode("\n", $requirements),
                    'development-requirements' => implode("\n", $developmentRequirements),
                ];
            }

            foreach ($package->getPullRequests() as $pullRequest) {
                $composerDetails         = $pullRequest->composerDetails;
                $requirements            = $this->requirement->parseRequirements($composerDetails);
                $developmentRequirements = $this->requirement->parseDevelopmentRequirements($composerDetails);

                $rows[] = [
                    'package'                  => $package->toString(),
                    'composer-package-name'    => $composerDetails->composerPackageName,
                    'head'                     => $pullRequest->head,
                    'requirements'             => implode("\n", $requirements),
                    'development-requirements' => implode("\n", $developmentRequirements),
                ];
            }

            $rows[] = new TableSeparator();
        }

        // Remove last table separator
        array_pop($rows);

        $table = new Table($output);
        $table
            ->setHeaders(array_keys($rows[0]))
            ->setRows($rows)
            ->render();

        return 0;
    }
}
