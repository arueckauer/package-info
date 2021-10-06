<?php

declare(strict_types=1);

namespace PackageInfoTest\Composer\Json;

use PackageInfo\Composer\Json\UrlComposer;
use PHPUnit\Framework\TestCase;

class UrlComposerTest extends TestCase
{
    public static function urls(): array
    {
        return [
            [
                'https://raw.githubusercontent.com/mezzio/mezzio-twigrenderer/2.8.x/composer.json',
                'mezzio',
                'mezzio-twigrenderer',
                '2.8.x',
            ],
            [
                'https://raw.githubusercontent.com/mezzio/mezzio-twigrenderer/2.7.0/composer.json',
                'mezzio',
                'mezzio-twigrenderer',
                '2.7.0',
            ],
            [
                'https://raw.githubusercontent.com/arueckauer/mezzio-twigrenderer/doc/twig-link/composer.json',
                'arueckauer',
                'mezzio-twigrenderer',
                'doc/twig-link',
            ],
        ];
    }

    /**
     * @covers \PackageInfo\Composer\Json\UrlComposer::__invoke
     * @dataProvider urls
     */
    public function test__invoke(
        string $expected,
        string $owner,
        string $repository,
        string $head
    ): void {
        $urlComposer = new UrlComposer();

        self::assertSame(
            $expected,
            $urlComposer($owner, $repository, $head)
        );
    }
}
