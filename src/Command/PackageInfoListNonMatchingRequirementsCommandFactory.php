<?php

declare(strict_types=1);

namespace PackageInfo\Command;

use PackageInfo\Information\Requirement;
use Psr\Container\ContainerInterface;

final class PackageInfoListNonMatchingRequirementsCommandFactory
{
    public function __invoke(ContainerInterface $container): PackageInfoListNonMatchingRequirementsCommand
    {
        $config = $container->get('config');

        return new PackageInfoListNonMatchingRequirementsCommand(
            $config['cache_file_path'],
            $container->get(Requirement::class)
        );
    }
}
