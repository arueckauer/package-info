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
        $organization = 'test-org';

        $packageA = new Package($organization, 'repo-a', false);
        $packageB = new Package($organization, 'repo-b', true);

        $expected = new PackageContainer($packageA, $packageB);

        $cacheContent = json_encode([
            'organization' => $organization,
            'generated_at' => '2026-01-01T00:00:00+00:00',
            'repositories' => [
                [
                    'name' => 'test-org/repo-a',
                    'organization' => $organization,
                    'repository' => 'repo-a',
                    'is_archived' => false,
                    'heads' => [],
                ],
                [
                    'name' => 'test-org/repo-b',
                    'organization' => $organization,
                    'repository' => 'repo-b',
                    'is_archived' => true,
                    'heads' => [],
                ],
            ],
        ], JSON_THROW_ON_ERROR);

        $root = vfsStream::setup();
        vfsStream::newFile('test-org.json')->at($root)->setContent($cacheContent);

        static::assertEquals($expected, $this->cache($root->url())->read($organization));
    }

    /** @throws ExpectationFailedException */
    public function test_read_returns_empty_PackageContainer_for_missing_file(): void
    {
        $cacheDirectory = vfsStream::url('home');

        static::assertEquals(new PackageContainer(), $this->cache($cacheDirectory)->read('test-org'));
    }

    /** @throws ExpectationFailedException */
    public function test_read_returns_empty_PackageContainer_for_empty_file(): void
    {
        $root = vfsStream::setup();
        vfsStream::newFile('test-org.json')->at($root)->setContent('');

        static::assertEquals(new PackageContainer(), $this->cache($root->url())->read('test-org'));
    }

    /**
     * @throws CacheFileNotWritable
     * @throws ExpectationFailedException
     */
    public function test_write(): void
    {
        $root = vfsStream::setup();
        $cacheFile = vfsStream::newFile('test-org.json')->at($root)->setContent('');

        $packageContainer = new PackageContainer(new Package('millennial-falcon', 'hyperdrive', false));
        $this->cache($root->url())->write('test-org', $packageContainer);

        static::assertNotSame('', $cacheFile->getContent());
        static::assertStringContainsString('"organization"', $cacheFile->getContent());
        static::assertStringContainsString('"generated_at"', $cacheFile->getContent());
        static::assertStringContainsString('millennial-falcon', $cacheFile->getContent());
    }

    private function cache(string $cacheDirectory): Cache
    {
        return new Cache($cacheDirectory, new JsonSerializer());
    }
}
