<?php

declare(strict_types=1);

namespace PackageInfoTest\Composer\Json;

use org\bovigo\vfs\vfsStream;
use PackageInfo\Composer\Json\FileReader;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

use function assert;
use function dirname;
use function file_get_contents;
use function is_string;

#[CoversClass(FileReader::class)]
final class FileReaderTest extends TestCase
{
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

        $content = file_get_contents(dirname(__DIR__, 2) . '/TestAsset/composer.json');
        assert(is_string($content));

        $root         = vfsStream::setup();
        $composerJson = vfsStream::newFile('composer.json')
            ->at($root)
            ->setContent($content);

        self::assertSame(
            $expected,
            (new FileReader())($composerJson->url())
        );
    }
}
