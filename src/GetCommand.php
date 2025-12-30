<?php

declare(strict_types=1);

namespace PackageInfo;

use Override;
use PackageInfo\Exception\PackageNotFoundException;
use PackageInfo\Output\Table\Row;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function array_keys;
use function sprintf;

final class GetCommand extends Command
{
    public function __construct(
        private readonly PackageContainer $packageContainer,
        private readonly Row $row,
    ) {
        parent::__construct();
    }

    #[Override]
    public function configure(): void
    {
        $this->setName('get');
        $this->setDescription('Lists all package information for given package');
        $this->addArgument('package-name', InputArgument::REQUIRED, 'Name of the package (vendor/project)');
    }

    #[Override]
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $packageName = $input->getArgument('package-name');

        $output->writeln(sprintf(
            '<comment>Retrieving package information for </comment><info>%s</info>',
            $packageName
        ));

        if (! $this->packageContainer->has($packageName)) {
            throw PackageNotFoundException::byPackage($packageName);
        }

        $package = $this->packageContainer->get($packageName);
        $rows    = [];

        foreach ($package->heads as $head) {
            $rows[] = ($this->row)($packageName, $head);
        }

        $table = new Table($output);
        $table
            ->setHeaders(array_keys($rows[0]))
            ->setRows($rows)
            ->render();

        return 0;
    }
}
