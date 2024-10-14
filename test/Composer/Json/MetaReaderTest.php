<?php

declare(strict_types=1);

namespace PackageInfoTest\Composer\Json;

use PackageInfo\Composer\Json\MetaReader;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MetaReader::class)]
class MetaReaderTest extends TestCase
{
    private static array $composer = [
        'name'        => 'millennial-falcon/hyperdrive',
        'require'     => [
            'php' => '^7.3 || ~8.0.0 || ~8.1.0',
        ],
        'require-dev' => [
            'laminas/laminas-coding-standard' => '~2.3.0',
            'phpunit/phpunit'                 => '^9.5.10',
        ],
    ];

    public function test_getPackageName(): void
    {
        $metaReader = new MetaReader();
        $metaReader->setComposer(static::$composer);

        self::assertEquals(
            'millennial-falcon/hyperdrive',
            $metaReader->getPackageName()
        );
    }

    public function test_isComposerJsonPresent(): void
    {
        $metaReader = new MetaReader();
        $metaReader->setComposer(static::$composer);

        self::assertTrue(
            $metaReader->isComposerJsonPresent()
        );
    }

    public function test_getRequirements(): void
    {
        $expected   = [
            'php' => '^7.3 || ~8.0.0 || ~8.1.0',
        ];
        $metaReader = new MetaReader();
        $metaReader->setComposer(static::$composer);

        self::assertEquals(
            $expected,
            $metaReader->getRequirements()
        );
    }

    public function test_getDevelopmentRequirements(): void
    {
        $expected = [
            'laminas/laminas-coding-standard' => '~2.3.0',
            'phpunit/phpunit'                 => '^9.5.10',
        ];

        $metaReader = new MetaReader();
        $metaReader->setComposer(static::$composer);

        self::assertEquals(
            $expected,
            $metaReader->getDevelopmentRequirements()
        );
    }
}
