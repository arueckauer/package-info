<?php

declare(strict_types=1);

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;

$cacheDirectory = null;
$cacheClass     = null;
if (getenv('CI')) {
    $cacheDirectory = '/tmp/rector';
    $cacheClass     = FileCacheStorage::class;
}

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/bin',
        __DIR__ . '/config',
        __DIR__ . '/src',
        __DIR__ . '/test',
    ])
    ->withSets([
        LevelSetList::UP_TO_PHP_85,
    ])
    ->withComposerBased(
        phpunit: true,
    )
    ->withCache($cacheDirectory, $cacheClass);
