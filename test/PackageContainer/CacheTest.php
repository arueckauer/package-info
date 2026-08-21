<?php

declare(strict_types=1);

namespace PackageInfoTest\PackageContainer;

use Exception;
use JsonException;
use org\bovigo\vfs\vfsStream;
use PackageInfo\Package;
use PackageInfo\PackageContainer;
use PackageInfo\PackageContainer\Cache;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

#[CoversClass(Cache::class)]
final class CacheTest extends TestCase
{
    /** @throws ExpectationFailedException */
    public function test__destruct_writes_cache(): void
    {
        $root = vfsStream::setup();
        $cacheFile = vfsStream::newFile('test-cache-file')->at($root)->setContent('');

        $cache = $this->cache($cacheFile->url());
        $cache->getPackageContainer()->add(new Package('millennial-falcon', 'hyperdrive', false));
        unset($cache);

        static::assertNotSame('', $cacheFile->getContent());
        static::assertStringContainsString('"organization"', $cacheFile->getContent());
    }

    /**
     * @throws ExpectationFailedException
     * @throws JsonException
     */
    public function test_getPackageContainer(): void
    {
        $packageA = new Package('test-org', 'repo-a', false);
        $packageB = new Package('test-org', 'repo-b', true);

        $expected = new PackageContainer($packageA, $packageB);

        $cacheContent = json_encode([
            'organization' => 'test-org',
            'generated_at' => '2026-01-01T00:00:00+00:00',
            'repositories' => [
                [
                    'name'         => 'test-org/repo-a',
                    'organization' => 'test-org',
                    'repository'   => 'repo-a',
                    'is_archived'  => false,
                    'heads'        => [],
                ],
                [
                    'name'         => 'test-org/repo-b',
                    'organization' => 'test-org',
                    'repository'   => 'repo-b',
                    'is_archived'  => true,
                    'heads'        => [],
                ],
            ],
        ], JSON_THROW_ON_ERROR);

        $root      = vfsStream::setup();
        $cacheFile = vfsStream::newFile('test-cache-file')->at($root)->setContent($cacheContent);

        static::assertEquals($expected, $this->cache($cacheFile->url())->getPackageContainer());
    }

    /** @throws ExpectationFailedException */
    public function test_getPackageContainer_initializes_empty_PackageContainer_for_missing_cache_file(): void
    {
        $home = vfsStream::setup('home');

        $filePath = vfsStream::url('home') . '/cache.json';

        static::assertEquals(new PackageContainer(), $this->cache($filePath)->getPackageContainer());
    }

    /**
     * @throws Exception
     */
    public function test_write(): void
    {
        $root = vfsStream::setup();
        $cacheFile = vfsStream::newFile('test-cache-file')->at($root)->setContent('');

        $cache = $this->cache($cacheFile->url());
        $cache->getPackageContainer()->add(new Package('millennial-falcon', 'hyperdrive', false));
        $cache->write();

        static::assertNotSame('', $cacheFile->getContent());
        static::assertStringContainsString('"organization"', $cacheFile->getContent());
        static::assertStringContainsString('"generated_at"', $cacheFile->getContent());
        static::assertStringContainsString('millennial-falcon', $cacheFile->getContent());
    }

    private function cache(string $cacheFilePath): Cache
    {
        return new Cache(new PackageContainer(), $cacheFilePath, 'test-org');
    }
}
