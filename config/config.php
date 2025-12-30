<?php

declare(strict_types=1);

use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\ConfigAggregator\PhpFileProvider;

$realPath = realpath(__DIR__);
assert(is_string($realPath));

$aggregator = new ConfigAggregator([
    Mezzio\Helper\ConfigProvider::class,
    Mezzio\ConfigProvider::class,
    Mezzio\Router\ConfigProvider::class,
    PackageInfo\ConfigProvider::class,
    new PhpFileProvider($realPath . '/autoload/{{,*.}global,{,*.}local}.php'),
]);

return $aggregator->getMergedConfig();
