<?php

declare(strict_types=1);

namespace PackageInfo\Output\Table;

use PackageInfo\Requirement\Checker;
use PackageInfo\Requirement\Renderer;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class RowFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): Row
    {
        return new Row(
            $container->get(Checker::class),
            $container->get(Renderer::class)
        );
    }
}
