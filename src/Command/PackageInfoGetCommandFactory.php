<?php

declare(strict_types=1);

namespace PackageInfo\Command;

use PackageInfo\Information\Requirement;
use Psr\Container\ContainerInterface;

class PackageInfoGetCommandFactory
{
    public function __invoke(ContainerInterface $container): PackageInfoGetCommand
    {
        $config = $container->get('config');

        return new PackageInfoGetCommand(
            $config['cache_file_path'],
            $container->get(Requirement::class)
        );
    }
}
