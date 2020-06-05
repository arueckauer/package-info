<?php

declare(strict_types=1);

namespace PackageInfo\Command;

use PackageInfo\Command\Exception\CacheNotFoundException;
use PackageInfo\Command\Exception\PackageNotFoundException;
use PackageInfo\Information\Package;
use PackageInfo\Information\Requirement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function file_exists;
use function file_get_contents;
use function implode;
use function sprintf;
use function unserialize;

class PackageInfoGetCommand extends Command
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
        $this->setName('package-info:get')
            ->setDescription('List all package information for given package')
            ->addArgument('package-name', InputArgument::REQUIRED, 'Name of the package (vendor/project)');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        if (! file_exists($this->cacheFilePath)) {
            throw CacheNotFoundException::byFilename($this->cacheFilePath);
        }

        $packageName = $input->getArgument('package-name');

        $output->writeln(sprintf(
            '<comment>Retrieving package information for </comment><info>%s</info>',
            $packageName
        ));

        $cacheContent = file_get_contents($this->cacheFilePath);
        /*** @var Package[] $packages */
        $packages = unserialize($cacheContent, [false]);

        $foundPackage = false;
        foreach ($packages as $package) {
            if ($package->toString() === $packageName) {
                $foundPackage = true;
                break;
            }
        }

        if (! isset($package) || false === $foundPackage) {
            throw PackageNotFoundException::byPackage($packageName);
        }

        $rows = [];

        foreach ($package->getBranches() as $branch) {
            $requirements            = $this->requirement->parseRequirements($branch);
            $developmentRequirements = $this->requirement->parseDevelopmentRequirements($branch);

            $rows[] = [
                'package'                 => $package->toString(),
                'branch'                  => $branch->getName(),
                'requirements'            => implode("\n", $requirements),
                'developmentRequirements' => implode("\n", $developmentRequirements),
            ];
        }

        $headers = [
            'package',
            'branch',
            'requirements',
            'developmentRequirements',
        ];
        $table   = new Table($output);
        $table
            ->setHeaders($headers)
            ->setRows($rows)
            ->render();

        return 0;
    }
}
