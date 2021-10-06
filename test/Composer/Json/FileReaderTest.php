<?php

declare(strict_types=1);

namespace PackageInfoTest\Composer\Json;

use org\bovigo\vfs\vfsStream;
use PackageInfo\Composer\Json\FileReader;
use PHPUnit\Framework\TestCase;

use function dirname;
use function file_get_contents;

class FileReaderTest extends TestCase
{
    /**
     * @covers \PackageInfo\Composer\Json\FileReader::__invoke
     */
    public function test__invoke(): void
    {
        $expected = [
            'name'        => 'millennial-falcon/hyperdrive',
            'require'     => [
                'php' => '^7.3 || ~8.0.0 || ~8.1.0',
            ],
            'require-dev' => [
                'laminas/laminas-coding-standard' => '~2.3.0',
                'phpunit/phpunit'                 => '^9.5.10',
            ],
        ];

        $root         = vfsStream::setup();
        $composerJson = vfsStream::newFile('composer.json')
            ->at($root)
            ->setContent(file_get_contents(dirname(__DIR__, 2) . '/TestAsset/composer.json'));

        self::assertSame(
            $expected,
            (new FileReader())($composerJson->url())
        );
    }
}
