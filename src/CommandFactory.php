<?php

declare(strict_types=1);

namespace PackageInfo;

use PackageInfo\Output\Table\Row;
use Psr\Container\ContainerInterface;

class CommandFactory
{
    public function __invoke(ContainerInterface $container): ListCommand
    {
        return new ListCommand(
            $container->get(PackageContainer::class),
            $container->get(Row::class)
        );
    }
}
