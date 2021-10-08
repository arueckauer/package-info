<?php

declare(strict_types=1);

namespace PackageInfo\Composer\Json;

use Psr\Container\ContainerInterface;

class UrlComposerFactory
{
    public function __invoke(ContainerInterface $container): UrlComposer
    {
        return new UrlComposer();
    }
}
