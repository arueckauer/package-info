<?php

declare(strict_types=1);

namespace PackageInfo\Command;

use PackageInfo\Output\Table\Row;
use PackageInfo\PackageContainer;
use Psr\Container\ContainerInterface;

class PackageInfoListCommandFactory
{
    public function __invoke(ContainerInterface $container): PackageInfoListCommand
    {
        return new PackageInfoListCommand(
            $container->get(PackageContainer::class),
            $container->get(Row::class)
        );
    }
}
