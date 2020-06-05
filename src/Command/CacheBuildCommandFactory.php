<?php

declare(strict_types=1);

namespace PackageInfo\Command;

use Github\Client;
use Psr\Container\ContainerInterface;

class CacheBuildCommandFactory
{
    public function __invoke(ContainerInterface $container): CacheBuildCommand
    {
        $config = $container->get('config');

        return new CacheBuildCommand(
            $container->get(Client::class),
            $config['organizations'],
            $config['ignore_repositories'],
            $config['ignore_branches'],
            $config['cache_file_path']
        );
    }
}
