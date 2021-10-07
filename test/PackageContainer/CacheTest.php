<?php

declare(strict_types=1);

namespace PackageInfoTest\PackageContainer;

use org\bovigo\vfs\vfsStream;
use PackageInfo\Package;
use PackageInfo\PackageContainer;
use PackageInfo\PackageContainer\Cache;
use PHPUnit\Framework\TestCase;

class CacheTest extends TestCase
{
    /**
     * @covers \PackageInfo\PackageContainer\Cache::__destruct
     */
    public function test__desctruct_writes_cache(): void
    {
        $root      = vfsStream::setup();
        $cacheFile = vfsStream::newFile('test-cache-file')
            ->at($root)
            ->setContent('');

        $cache = new Cache($cacheFile->url());
        $cache->getPackageContainer()->add(new Package('millennial-falcon', 'hyperdrive'));
        unset($cache);

        self::assertNotSame(
            '',
            $cacheFile->getContent()
        );
    }

    /**
     * @covers \PackageInfo\PackageContainer\Cache::getPackageContainer
     */
    public function test_getPackageContainer(): void
    {
        $packageA = new Package('millennial-falcon', 'hyperdrive');
        $packageB = new Package('x-wing', 'hyperdrive');
        $packageC = new Package('b-wing', 'hyperdrive');

        $expected = new PackageContainer(
            $packageA,
            $packageB,
            $packageC
        );

        // phpcs:ignore
        $cacheContent = 'a:3:{s:17:"b-wing/hyperdrive";O:19:"PackageInfo\Package":3:{s:12:"organization";s:6:"b-wing";s:10:"repository";s:10:"hyperdrive";s:5:"heads";a:0:{}}s:28:"millennial-falcon/hyperdrive";O:19:"PackageInfo\Package":3:{s:12:"organization";s:17:"millennial-falcon";s:10:"repository";s:10:"hyperdrive";s:5:"heads";a:0:{}}s:17:"x-wing/hyperdrive";O:19:"PackageInfo\Package":3:{s:12:"organization";s:6:"x-wing";s:10:"repository";s:10:"hyperdrive";s:5:"heads";a:0:{}}}';

        $root      = vfsStream::setup();
        $cacheFile = vfsStream::newFile('test-cache-file')
            ->at($root)
            ->setContent($cacheContent);

        self::assertEquals(
            $expected,
            (new Cache($cacheFile->url()))->getPackageContainer()
        );
    }

    /**
     * @covers \PackageInfo\PackageContainer\Cache::getPackageContainer
     */
    public function test_getPackageContainer_initializes_empty_PackageContainer_for_invalid_cache_file(): void
    {
        self::assertEquals(
            new PackageContainer(),
            (new Cache('file-does-not-exist'))->getPackageContainer()
        );
    }

    /**
     * @covers \PackageInfo\PackageContainer\Cache::write
     */
    public function test_write(): void
    {
        $root      = vfsStream::setup();
        $cacheFile = vfsStream::newFile('test-cache-file')
            ->at($root)
            ->setContent('');

        $cache = new Cache($cacheFile->url());
        $cache->getPackageContainer()->add(new Package('millennial-falcon', 'hyperdrive'));
        $cache->write();

        self::assertNotSame(
            '',
            $cacheFile->getContent()
        );
    }
}
