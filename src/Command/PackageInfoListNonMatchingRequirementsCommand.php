<?php

declare(strict_types=1);

namespace PackageInfo\Command;

use PackageInfo\Command\Exception\CacheNotFoundException;
use PackageInfo\Information\Package;
use PackageInfo\Information\Repository\ComposerDetails;
use PackageInfo\Information\Requirement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function array_intersect_key;
use function array_keys;
use function array_pop;
use function count;
use function file_exists;
use function file_get_contents;
use function is_array;
use function rtrim;
use function sprintf;
use function unserialize;

use const PHP_EOL;

final class PackageInfoListNonMatchingRequirementsCommand extends Command
{
    public const OPTION_PACKAGE_NAMES_ONLY       = 'package-names-only';
    public const OPTION_PACKAGE_NAMES_ONLY_SHORT = 'o';

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
            ->setDescription('List all package information')
            ->addOption(
                self::OPTION_PACKAGE_NAMES_ONLY,
                self::OPTION_PACKAGE_NAMES_ONLY_SHORT,
                null,
                'List only package names instead of a full table.'
            );
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        if (! file_exists($this->cacheFilePath)) {
            throw CacheNotFoundException::byFilename($this->cacheFilePath);
        }

        $cacheContent = file_get_contents($this->cacheFilePath);
        /*** @var Package[] $packages */
        $packages = unserialize($cacheContent, [false]);

        if (! $input->getOption(self::OPTION_PACKAGE_NAMES_ONLY)) {
            $output->writeln(sprintf(
                '<comment>Showing information for <info>%s</info> packages</comment>',
                count($packages)
            ));
        }

        $rows = [];

        foreach ($packages as $key => $package) {
            [$unmatchedRequirements, $unmatchedDevelopmentRequirements] = $this->unmatchedRequirements($package);

            if ($unmatchedRequirements === [] && $unmatchedDevelopmentRequirements === []) {
                continue;
            }

            $rows[] = [
                'package'                  => $package->toString(),
                'requirements'             => $this->requirementsToString($unmatchedRequirements),
                'development requirements' => $this->requirementsToString($unmatchedDevelopmentRequirements),
            ];

            $rows[] = new TableSeparator();
        }

        // Remove last table separator
        array_pop($rows);

        if ($input->getOption(self::OPTION_PACKAGE_NAMES_ONLY)) {
            $output->writeln($this->renderPackagesOnly($rows));

            return 0;
        }

        $table = new Table($output);
        $table
            ->setHeaders(array_keys($rows[0] ?? []))
            ->setRows($rows)
            ->render();

        return 0;
    }

    private function unmatchedRequirements(Package $package): array
    {
        // First, assume all as unmatched
        $unmatchedRequirements            = $this->requirement->requirements();
        $unmatchedDevelopmentRequirements = $this->requirement->developmentRequirements();

        foreach ($package->getBranches() as $branch) {
            $composerDetails = $branch->composerDetails;
            [
                $unmatchedRequirements,
                $unmatchedDevelopmentRequirements,
            ]                = $this->detectUnmatchedRequirements(
                $composerDetails,
                $unmatchedRequirements,
                $unmatchedDevelopmentRequirements
            );

            if ($unmatchedRequirements === [] && $unmatchedDevelopmentRequirements === []) {
                return [[], []];
            }
        }

        foreach ($package->getPullRequests() as $pullRequest) {
            $composerDetails = $pullRequest->composerDetails;

            [
                $unmatchedRequirements,
                $unmatchedDevelopmentRequirements,
            ] = $this->detectUnmatchedRequirements(
                $composerDetails,
                $unmatchedRequirements,
                $unmatchedDevelopmentRequirements
            );

            if ($unmatchedRequirements === [] && $unmatchedDevelopmentRequirements === []) {
                return [[], []];
            }
        }

        return [$unmatchedRequirements, $unmatchedDevelopmentRequirements];
    }

    private function detectUnmatchedRequirements(
        ComposerDetails $composerDetails,
        array $unmatchedRequirements,
        array $unmatchedDevelopmentRequirements
    ) {
        if ($unmatchedRequirements !== []) {
            $unmatchedRequirementsForBranch = $this->requirement->unmatchedRequirements($composerDetails);
            $unmatchedRequirements          = array_intersect_key(
                $unmatchedRequirementsForBranch,
                $unmatchedRequirements
            );
        }

        if ($unmatchedDevelopmentRequirements !== []) {
            $unmatchedDevelopmentRequirementsForBranch = $this->requirement->ummatchedDevelopmentRequirements($composerDetails);
            $unmatchedDevelopmentRequirements          = array_intersect_key(
                $unmatchedDevelopmentRequirements,
                $unmatchedDevelopmentRequirementsForBranch
            );
        }

        return [$unmatchedRequirements, $unmatchedDevelopmentRequirements];
    }

    private function requirementsToString(array $unmatchedRequirements): string
    {
        $requirements = "";
        foreach ($unmatchedRequirements as $package => $version) {
            $requirements .= sprintf('%s: %s', $package, $version) . PHP_EOL;
        }

        return rtrim($requirements);
    }

    private function renderPackagesOnly(array $rows): string
    {
        $packages = "";
        foreach ($rows as $row) {
            if (! is_array($row) || ! isset($row['package'])) {
                continue;
            }

            $packages .= $row['package'] . " ";
        }

        return rtrim($packages);
    }
}
