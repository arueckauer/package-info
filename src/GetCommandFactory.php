<?php

declare(strict_types=1);

namespace PackageInfo;

use PackageInfo\Output\Table\Row;
use Psr\Container\ContainerInterface;

class GetCommandFactory
{
    public function __invoke(ContainerInterface $container): GetCommand
    {
        return new GetCommand(
            $container->get(PackageContainer::class),
            $container->get(Row::class)
        );
    }
}
