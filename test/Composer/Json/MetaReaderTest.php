<?php

declare(strict_types=1);

namespace PackageInfoTest\Composer\Json;

use PackageInfo\Composer\Json\MetaReader;
use PHPUnit\Framework\TestCase;

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

    /**
     * @covers \PackageInfo\Composer\Json\MetaReader::getPackageName
     */
    public function test_getPackageName(): void
    {
        $metaReader = new MetaReader();
        $metaReader->setComposer(static::$composer);

        self::assertEquals(
            'millennial-falcon/hyperdrive',
            $metaReader->getPackageName()
        );
    }

    /**
     * @covers \PackageInfo\Composer\Json\MetaReader::isComposerJsonPresent
     */
    public function test_isComposerJsonPresent(): void
    {
        $metaReader = new MetaReader();
        $metaReader->setComposer(static::$composer);

        self::assertTrue(
            $metaReader->isComposerJsonPresent()
        );
    }

    /**
     * @covers \PackageInfo\Composer\Json\MetaReader::getRequirements
     */
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

    /**
     * @covers \PackageInfo\Composer\Json\MetaReader::getDevelopmentRequirements
     */
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
