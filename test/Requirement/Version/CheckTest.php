<?php

declare(strict_types=1);

namespace PackageInfoTest\Requirement\Version;

use Composer\Semver\VersionParser;
use PackageInfo\Requirement\Version\Check;
use PHPUnit\Framework\TestCase;

class CheckTest extends TestCase
{
    public static function checks(): array
    {
        return [
            [true, '7.3.0', '^7.3 || ~8.0.0 || ~8.1.0'],
            [true, '7.3.0', '^7.3 || ^8.0'],
            [false, '7.3.0', '^7.2'],
            [false, '7.3.0', '^7.1'],
            [false, '7.3.0', '^5.6 || ^7.0'],
        ];
    }

    /**
     * @covers \PackageInfo\Requirement\Version\Check::__invoke
     * @dataProvider checks
     */
    public function test__invoke(bool $expected, string $minimumVersion, string $constraints): void
    {
        $check = new Check(new VersionParser());
        self::assertSame($expected, $check($minimumVersion, $constraints));
    }
}
