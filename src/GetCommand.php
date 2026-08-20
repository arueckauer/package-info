<?php

declare(strict_types=1);

namespace PackageInfo;

use Override;
use PackageInfo\Exception\PackageNotFound;
use PackageInfo\Output\Table\Row;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function array_keys;
use function assert;
use function is_string;
use function sprintf;

#[AsCommand(name: 'get', description: 'Lists all package information for given package')]
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
        $this->addArgument('package-name', InputArgument::REQUIRED, 'Name of the package (vendor/project)');
    }

    /**
     * @throws PackageNotFound
     */
    #[Override]
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $packageName = $input->getArgument('package-name');
        assert(is_string($packageName));

        $output->writeln(sprintf(
            '<comment>Retrieving package information for </comment><info>%s</info>',
            $packageName,
        ));

        if (!$this->packageContainer->has($packageName)) {
            throw PackageNotFound::byPackage($packageName);
        }

        $package = $this->packageContainer->get($packageName);
        $rows = [];

        foreach ($package->heads as $head) {
            $rows[] = ($this->row)($packageName, $head);
        }

        $table = new Table($output);
        $table->setHeaders(array_keys($rows[0]))->setRows($rows)->render();

        return 0;
    }
}
