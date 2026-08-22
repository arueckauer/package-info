<?php

declare(strict_types=1);

namespace PackageInfoTest\PackageContainer;

use JsonException;
use org\bovigo\vfs\vfsStream;
use PackageInfo\Package;
use PackageInfo\PackageContainer;
use PackageInfo\PackageContainer\Cache;
use PackageInfo\PackageContainer\Exception\CacheFileNotWritable;
use PackageInfo\PackageContainer\JsonSerializer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

#[CoversClass(Cache::class)]
final class CacheTest extends TestCase
{
    /**
     * @throws ExpectationFailedException
     * @throws JsonException
     */
    public function test_read_returns_deserialized_PackageContainer(): void
    {
        $packageA = new Package('test-org', 'repo-a', false);
        $packageB = new Package('test-org', 'repo-b', true);

        $expected = new PackageContainer($packageA, $packageB);

        $cacheContent = json_encode([
            'organization' => 'test-org',
            'generated_at' => '2026-01-01T00:00:00+00:00',
            'repositories' => [
                [
                    'name' => 'test-org/repo-a',
                    'organization' => 'test-org',
                    'repository' => 'repo-a',
                    'is_archived' => false,
                    'heads' => [],
                ],
                [
                    'name' => 'test-org/repo-b',
                    'organization' => 'test-org',
                    'repository' => 'repo-b',
                    'is_archived' => true,
                    'heads' => [],
                ],
            ],
        ], JSON_THROW_ON_ERROR);

        $root = vfsStream::setup();
        $cacheFile = vfsStream::newFile('test-cache-file')->at($root)->setContent($cacheContent);

        static::assertEquals($expected, $this->cache($cacheFile->url())->read());
    }

    /** @throws ExpectationFailedException */
    public function test_read_returns_empty_PackageContainer_for_missing_file(): void
    {
        $filePath = vfsStream::url('home') . '/cache.json';

        static::assertEquals(new PackageContainer(), $this->cache($filePath)->read());
    }

    /** @throws ExpectationFailedException */
    public function test_read_returns_empty_PackageContainer_for_empty_file(): void
    {
        $root = vfsStream::setup();
        $cacheFile = vfsStream::newFile('cache.json')->at($root)->setContent('');

        static::assertEquals(new PackageContainer(), $this->cache($cacheFile->url())->read());
    }

    /**
     * @throws CacheFileNotWritable
     * @throws ExpectationFailedException
     */
    public function test_write(): void
    {
        $root = vfsStream::setup();
        $cacheFile = vfsStream::newFile('cache.json')->at($root)->setContent('');

        $container = new PackageContainer(new Package('millennial-falcon', 'hyperdrive', false));
        $this->cache($cacheFile->url())->write('test-org', $container);

        static::assertNotSame('', $cacheFile->getContent());
        static::assertStringContainsString('"organization"', $cacheFile->getContent());
        static::assertStringContainsString('"generated_at"', $cacheFile->getContent());
        static::assertStringContainsString('millennial-falcon', $cacheFile->getContent());
    }

    private function cache(string $cacheFilePath): Cache
    {
        return new Cache($cacheFilePath, new JsonSerializer());
    }
}
