<?php

declare(strict_types=1);

namespace PackageInfoTest\PackageContainer;

use JsonException;
use PackageInfo\Package;
use PackageInfo\PackageContainer;
use PackageInfo\PackageContainer\JsonSerializer;
use PackageInfo\Repository\Head;
use PackageInfo\Repository\Head\Type;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\GeneratorNotSupportedException;
use PHPUnit\Framework\TestCase;

#[CoversClass(JsonSerializer::class)]
final class JsonSerializerTest extends TestCase
{
    /**
     * @throws ExpectationFailedException
     * @throws Exception
     * @throws GeneratorNotSupportedException
     * @throws JsonException
     */
    public function test_serialize_produces_valid_json(): void
    {
        $serializer = new JsonSerializer();
        $container = new PackageContainer();
        $container->add(new Package('test-org', 'my-repo', false));

        $json = $serializer->serialize($container, 'test-org');
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        static::assertIsArray($data);
        static::assertSame('test-org', $data['organization']);
        static::assertArrayHasKey('generated_at', $data);
        static::assertIsArray($data['repositories']);
        static::assertCount(1, $data['repositories']);
        static::assertIsArray($data['repositories'][0]);
        static::assertSame('test-org/my-repo', $data['repositories'][0]['name']);
        static::assertSame('test-org', $data['repositories'][0]['organization']);
        static::assertSame('my-repo', $data['repositories'][0]['repository']);
        static::assertFalse($data['repositories'][0]['is_archived']);
        static::assertSame([], $data['repositories'][0]['heads']);
    }

    /**
     * @throws ExpectationFailedException
     * @throws Exception
     * @throws GeneratorNotSupportedException
     * @throws JsonException
     */
    public function test_serialize_includes_heads(): void
    {
        $serializer = new JsonSerializer();
        $head = new Head(
            'test-org/my-repo',
            Type::Branch->value,
            'main',
            true,
            ['php' => '^8.1'],
            ['phpunit/phpunit' => '^11.0'],
        );
        $package = new Package('test-org', 'my-repo', false)->withHead($head);
        $container = new PackageContainer();
        $container->add($package);

        $json = $serializer->serialize($container, 'test-org');
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        static::assertIsArray($data);
        static::assertIsArray($data['repositories'][0]);
        static::assertIsArray($data['repositories'][0]['heads']);
        $headData = $data['repositories'][0]['heads'][0];
        static::assertIsArray($headData);
        static::assertSame('branch', $headData['type']);
        static::assertSame('main', $headData['name']);
        static::assertTrue($headData['composer_json_present']);
        static::assertSame(['php' => '^8.1'], $headData['require']);
        static::assertSame(['phpunit/phpunit' => '^11.0'], $headData['require_dev']);
    }

    /** @throws ExpectationFailedException */
    public function test_roundtrip(): void
    {
        $serializer = new JsonSerializer();
        $head = new Head('test-org/my-repo', Type::Release->value, '1.0.0', true, ['php' => '^8.2'], []);
        $packageA = new Package('test-org', 'my-repo', false)->withHead($head);
        $packageB = new Package('test-org', 'archived-repo', true);

        $original = new PackageContainer();
        $original->add($packageA);
        $original->add($packageB);

        $json = $serializer->serialize($original, 'test-org');
        $loaded = $serializer->deserialize($json);

        static::assertEquals($original, $loaded);
    }

    /**
     * @throws ExpectationFailedException
     * @throws JsonException
     */
    public function test_deserialize_empty_repositories(): void
    {
        $serializer = new JsonSerializer();
        $json = json_encode([
            'organization' => 'empty-org',
            'generated_at' => '2026-01-01T00:00:00+00:00',
            'repositories' => [],
        ], JSON_THROW_ON_ERROR);

        $container = $serializer->deserialize($json);

        static::assertEquals(new PackageContainer(), $container);
    }
}
