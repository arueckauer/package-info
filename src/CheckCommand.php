<?php

declare(strict_types=1);

namespace PackageInfo;

use LogicException;
use PackageInfo\Requirement\Checker;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function array_keys;
use function count;
use function explode;
use function implode;
use function in_array;
use function is_countable;
use function sprintf;

class CheckCommand extends Command
{
    public function __construct(
        private readonly PackageContainer $packageContainer,
        private readonly Checker $checker,
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        $this->setName('check');
        $this->setDescription('Check all package information against given requirement');

        $this->addOption(
            'require',
            'r',
            InputOption::VALUE_OPTIONAL,
            'Requirement, e.g. `php:8.0`'
        );
        $this->addOption(
            'require-dev',
            'd',
            InputOption::VALUE_OPTIONAL,
            'Development requirement, e.g. `vimeo/psalm:4.11`'
        );
        $this->addOption(
            'vendor',
            null,
            InputOption::VALUE_OPTIONAL,
            'Filter vendor name, e.g. `mezzio`'
        );
        $this->addOption(
            'head-type',
            't',
            InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
            'branch, pull-request and/or release'
        );
        $this->addOption(
            'include-archived',
            'a',
            InputOption::VALUE_NONE,
            'Include archived repositories'
        );
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $require    = $input->getOption('require');
        $requireDev = $input->getOption('require-dev');
        $vendor     = $input->getOption('vendor');
        $headType   = $input->getOption('head-type');
        $archived   = $input->getOption('include-archived');

        if (
            null === $require
            && null === $requireDev
        ) {
            throw new LogicException('Either option "require" or "require-dev" must be provided.');
        }

        if (
            null !== $require
            && null !== $requireDev
        ) {
            throw new LogicException('Only one option "require" or "require-dev" must be provided.');
        }

        if (null !== $require) {
            $checkRequirement                          = true;
            [$requiredPackage, $lowestRequiredVersion] = explode(':', (string) $require);
            $this->checker->requirements               = [$requiredPackage => $lowestRequiredVersion];
            $this->checker->developmentRequirements    = [];

            $checkMessage = sprintf('for requirement <info>%s</info> ', $require);
        } else {
            $checkRequirement                          = false;
            [$requiredPackage, $lowestRequiredVersion] = explode(':', (string) $requireDev);
            $this->checker->requirements               = [];
            $this->checker->developmentRequirements    = [$requiredPackage => $lowestRequiredVersion];

            $checkMessage = sprintf('for development requirement <info>%s</info> ', $requireDev);
        }

        $packages = $this->packageContainer->all();
        $output->writeln(sprintf(
            '<comment>Checking <info>%s</info> packages</comment> ' . $checkMessage,
            count($packages)
        ));

        $headTypeCount   = is_countable($headType) ? count($headType) : 0;
        $rows            = [];
        $lastPackageName = null;

        foreach ($packages as $package) {
            if ($vendor !== null && $package->organization !== $vendor) {
                continue;
            }

            if (! $archived && $package->isArchived) {
                continue;
            }

            $greenHeads = [];
            foreach ($package->heads as $head) {
                if ($headTypeCount > 0 && ! in_array($head->headType, $headType, true)) {
                    continue;
                }

                $results = $checkRequirement
                    ? $this->checker->checkRequirements($head)
                    : $this->checker->checkDevelopmentRequirements($head);

                foreach ($results as $result) {
                    if ($result->hasRequirement && $result->isSupported) {
                        $greenHeads[] = sprintf('%s [%s]', $head->headName, (string) $result->versionConstraint);
                    }
                }
            }

            if (null !== $lastPackageName && $lastPackageName !== $package->toString()) {
                $rows[] = new TableSeparator();
            }
            $lastPackageName = $package->toString();

            $format = count($greenHeads) > 0 ? '<info>%s%s</info>' : '<comment>%s%s</comment>';
            $rows[] = [
                'package'     => sprintf(
                    $format,
                    $package->toString(),
                    $package->isArchived ? ' (archived)' : ''
                ),
                'green-heads' => implode("\n", $greenHeads),
            ];
        }

        if (0 === count($rows)) {
            $output->writeln('<info>No packages found</info>');
            return 0;
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
