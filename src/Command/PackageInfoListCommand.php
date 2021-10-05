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
            $lastPackageName = null;
            foreach ($package->getBranches() as $branch) {
                if (0 !== $key && $lastPackageName !== $package->getRepository()) {
                    $rows[] = new TableSeparator();
                }

                $requirements            = $this->requirement->parseRequirements($branch);
                $developmentRequirements = $this->requirement->parseDevelopmentRequirements($branch);

                $rows[] = [
                    'package'                  => $package->toString(),
                    'composer-package-name'    => $branch->getComposerPackageName(),
                    'branch'                   => $branch->getName(),
                    'requirements'             => implode("\n", $requirements),
                    'development-requirements' => implode("\n", $developmentRequirements),
                ];

                $lastPackageName = $package->getRepository();
            }
        }

        /** @psalm-var array<int, string> $firstRow */
        $firstRow = $rows[0];

        $table = new Table($output);
        $table
            ->setHeaders(array_keys($firstRow))
            ->setRows($rows)
            ->render();

        return 0;
    }
}
