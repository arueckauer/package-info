<?php

declare(strict_types=1);

namespace PackageInfo\Command;

use PackageInfo\Output\Table\Row;
use PackageInfo\PackageContainer;
use Psr\Container\ContainerInterface;

class PackageInfoGetCommandFactory
{
    public function __invoke(ContainerInterface $container): PackageInfoGetCommand
    {
        return new PackageInfoGetCommand(
            $container->get(PackageContainer::class),
            $container->get(Row::class)
        );
    }
}
