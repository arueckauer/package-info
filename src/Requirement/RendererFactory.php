<?php

declare(strict_types=1);

namespace PackageInfo\Requirement;

use Psr\Container\ContainerInterface;

class RendererFactory
{
    public function __invoke(ContainerInterface $container): Renderer
    {
        return new Renderer();
    }
}
