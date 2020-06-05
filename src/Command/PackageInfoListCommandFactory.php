<?php

declare(strict_types=1);

namespace PackageInfo\Command;

use PackageInfo\Information\Requirement;
use Psr\Container\ContainerInterface;

class PackageInfoListCommandFactory
{
    public function __invoke(ContainerInterface $container): PackageInfoListCommand
    {
        $config = $container->get('config');

        return new PackageInfoListCommand(
            $config['cache_file_path'],
            $container->get(Requirement::class)
        );
    }
}
