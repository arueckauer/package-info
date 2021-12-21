<?php

declare(strict_types=1);

namespace PackageInfo;

use PackageInfo\Output\Table\Row;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class GetCommandFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): GetCommand
    {
        return new GetCommand(
            $container->get(PackageContainer::class),
            $container->get(Row::class)
        );
    }
}
