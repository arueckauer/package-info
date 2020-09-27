<?php

declare(strict_types=1);

namespace PackageInfo\Command;

use PackageInfo\Command\Exception\CacheNotFoundException;
use PackageInfo\Command\Exception\PackageNotFoundException;
use PackageInfo\Information\Package;
use PackageInfo\Information\Repository\ComposerDetails;
use PackageInfo\Information\Requirement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function array_keys;
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
            $details = $branch->composerDetails;
            $rows[]  = $this->row($package, $details, $branch->getName());
        }

        foreach ($package->getPullRequests() as $pullRequest) {
            $details = $pullRequest->composerDetails;
            $rows[]  = $this->row($package, $details, $pullRequest->head);
        }

        $table = new Table($output);
        $table
            ->setHeaders(array_keys($rows[0]))
            ->setRows($rows)
            ->render();

        return 0;
    }

    private function row(Package $package, ComposerDetails $details, string $head): array
    {
        $requirements            = $this->requirement->parseRequirements($details);
        $developmentRequirements = $this->requirement->parseDevelopmentRequirements($details);

        return [
            'package'                  => $package->toString(),
            'composer-package-name'    => $details->composerPackageName,
            'head'                     => $head,
            'requirements'             => implode("\n", $requirements),
            'development-requirements' => implode("\n", $developmentRequirements),
        ];
    }
}
