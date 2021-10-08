<?php

declare(strict_types=1);

namespace PackageInfo;

use PackageInfo\Output\Table\Row;
use Psr\Container\ContainerInterface;

class AllCommandFactory
{
    public function __invoke(ContainerInterface $container): AllCommand
    {
        return new AllCommand(
            $container->get(PackageContainer::class),
            $container->get(Row::class)
        );
    }
}
