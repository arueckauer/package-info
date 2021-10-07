<?php

declare(strict_types=1);

namespace PackageInfo\Output\Table;

use PackageInfo\Information\Requirement;
use Psr\Container\ContainerInterface;

class RowFactory
{
    public function __invoke(ContainerInterface $container): Row
    {
        return new Row(
            $container->get(Requirement::class)
        );
    }
}
