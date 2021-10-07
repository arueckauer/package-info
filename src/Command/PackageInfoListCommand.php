<?php

declare(strict_types=1);

namespace PackageInfo\Command;

use PackageInfo\Output\Table\Row;
use PackageInfo\PackageContainer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function array_keys;
use function count;
use function sprintf;

class PackageInfoListCommand extends Command
{
    private PackageContainer $packageContainer;
    private Row $row;

    public function __construct(PackageContainer $packageContainer, Row $row)
    {
        $this->packageContainer = $packageContainer;
        $this->row              = $row;

        parent::__construct();
    }

    public function configure(): void
    {
        $this->setName('package-info:list');
        $this->setDescription('List all package information');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $packages = $this->packageContainer->all();

        $output->writeln(sprintf(
            '<comment>Showing information for <info>%s</info> packages</comment>',
            count($packages)
        ));
        $rows = [];

        $lastPackageName = null;
        foreach ($packages as $package) {
            foreach ($package->getHeads() as $head) {
                if (null !== $lastPackageName && $lastPackageName !== $package->toString()) {
                    $rows[] = new TableSeparator();
                }
                $lastPackageName = $package->toString();

                $rows[] = ($this->row)($lastPackageName, $head);
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
