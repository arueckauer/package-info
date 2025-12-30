<?php

declare(strict_types=1);

namespace PackageInfoTest\PackageContainer;

use org\bovigo\vfs\vfsStream;
use PackageInfo\Package;
use PackageInfo\PackageContainer;
use PackageInfo\PackageContainer\Cache;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Cache::class)]
class CacheTest extends TestCase
{
    public function test__desctruct_writes_cache(): void
    {
        $root      = vfsStream::setup();
        $cacheFile = vfsStream::newFile('test-cache-file')
            ->at($root)
            ->setContent('');

        $cache = new Cache($cacheFile->url());
        $cache->getPackageContainer()->add(new Package('millennial-falcon', 'hyperdrive', false));
        unset($cache);

        self::assertNotSame(
            '',
            $cacheFile->getContent()
        );
    }

    public function test_getPackageContainer(): void
    {
        $packageA = new Package('millennial-falcon', 'hyperdrive', false);
        $packageB = new Package('x-wing', 'hyperdrive', false);
        $packageC = new Package('b-wing', 'hyperdrive', true);

        $expected = new PackageContainer(
            $packageA,
            $packageB,
            $packageC
        );

        // phpcs:ignore
        $cacheContent = 'a:3:{s:17:"b-wing/hyperdrive";O:19:"PackageInfo\Package":4:{s:12:"organization";s:6:"b-wing";s:10:"repository";s:10:"hyperdrive";s:10:"isArchived";b:1;s:5:"heads";a:0:{}}s:28:"millennial-falcon/hyperdrive";O:19:"PackageInfo\Package":4:{s:12:"organization";s:17:"millennial-falcon";s:10:"repository";s:10:"hyperdrive";s:10:"isArchived";b:0;s:5:"heads";a:0:{}}s:17:"x-wing/hyperdrive";O:19:"PackageInfo\Package":4:{s:12:"organization";s:6:"x-wing";s:10:"repository";s:10:"hyperdrive";s:10:"isArchived";b:0;s:5:"heads";a:0:{}}}';

        $root      = vfsStream::setup();
        $cacheFile = vfsStream::newFile('test-cache-file')
            ->at($root)
            ->setContent($cacheContent);

        self::assertEquals(
            $expected,
            (new Cache($cacheFile->url()))->getPackageContainer()
        );
    }

    public function test_getPackageContainer_initializes_empty_PackageContainer_for_invalid_cache_file(): void
    {
        $home = vfsStream::setup('home');

        $filePath = vfsStream::url('home') . '/cache.dat';

        self::assertEquals(
            new PackageContainer(),
            (new Cache($filePath))->getPackageContainer()
        );

        self::assertTrue($home->hasChild('cache.dat'));
        self::assertFileExists($filePath);
    }

    public function test_write(): void
    {
        $root      = vfsStream::setup();
        $cacheFile = vfsStream::newFile('test-cache-file')
            ->at($root)
            ->setContent('');

        $cache = new Cache($cacheFile->url());
        $cache->getPackageContainer()->add(new Package('millennial-falcon', 'hyperdrive', false));
        $cache->write();

        self::assertNotSame(
            '',
            $cacheFile->getContent()
        );
    }
}
