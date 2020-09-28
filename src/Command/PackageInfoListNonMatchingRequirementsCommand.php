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

use function array_diff_key;
use function array_keys;
use function array_pop;
use function count;
use function file_exists;
use function file_get_contents;
use function implode;
use function sprintf;
use function unserialize;
use const PHP_EOL;

final class PackageInfoListNonMatchingRequirementsCommand extends Command
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
        $this->setName('package-info:list-non-matching-requirements')
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
            [$unmatchedRequirements, $unmatchedDevelopmentRequirements] = $this->unmatchedRequirements($package);


            if ($unmatchedRequirements === [] && $unmatchedDevelopmentRequirements === []) {
                continue;
            }

            $rows[] = [
                'package' => $package->toString(),
                'requirements' => $this->requirementsToString($unmatchedRequirements),
                'development requirements' => $this->requirementsToString($unmatchedDevelopmentRequirements),
            ];

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

    private function unmatchedRequirements(Package $package): array
    {
        // First, assume all as unmatched
        $unmatchedRequirements            = $this->requirement->requirements();
        $ummatchedDevelopmentRequirements = $this->requirement->developmentRequirements();

        foreach ($package->getBranches() as $branch) {
            $composerDetails                           = $branch->composerDetails;
            $unmatchedRequirementsForBranch            = $this->requirement->unmatchedRequirements($composerDetails);
            $ummatchedDevelopmentRequirementsForBranch = $this->requirement->ummatchedDevelopmentRequirements($composerDetails);

            $unmatchedRequirements            = array_diff_key($unmatchedRequirementsForBranch, $unmatchedRequirements);
            $ummatchedDevelopmentRequirements = array_diff_key($ummatchedDevelopmentRequirementsForBranch, $ummatchedDevelopmentRequirements);

            if ($unmatchedRequirements === [] && $unmatchedRequirementsForBranch === []) {
                return [[], []];
            }
        }

        foreach ($package->getPullRequests() as $pullRequest) {
            $composerDetails                           = $pullRequest->composerDetails;
            $unmatchedRequirementsForBranch            = $this->requirement->unmatchedRequirements($composerDetails);
            $ummatchedDevelopmentRequirementsForBranch = $this->requirement->ummatchedDevelopmentRequirements($composerDetails);

            $unmatchedRequirements            = array_diff_key($unmatchedRequirementsForBranch, $unmatchedRequirements);
            $ummatchedDevelopmentRequirements = array_diff_key($ummatchedDevelopmentRequirementsForBranch, $ummatchedDevelopmentRequirements);

            if ($unmatchedRequirements && $ummatchedDevelopmentRequirements) {
                return [[], []];
            }
        }

        return [$unmatchedRequirements, $ummatchedDevelopmentRequirements];
    }

    private function requirementsToString(array $unmatchedRequirements): string
    {
        $requirements = "";
        foreach ($unmatchedRequirements as $package => $version) {
            $requirements .= sprintf('%s: %s', $package, $version) . PHP_EOL;
        }

        return rtrim($requirements);
    }
}
