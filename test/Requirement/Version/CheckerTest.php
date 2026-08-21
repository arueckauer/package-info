<?php

declare(strict_types=1);

namespace PackageInfoTest\Requirement\Version;

use Composer\Semver\VersionParser;
use PackageInfo\Requirement\Version\Checker;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

#[CoversClass(Checker::class)]
final class CheckerTest extends TestCase
{
    public static function checks(): array
    {
        return [
            [true,  '7.3.0', '^7.3 || ~8.0.0 || ~8.1.0'],
            [true,  '7.3.0', '^7.3 || ^8.0'],
            [true,  '7.3.0', '^7.2'],
            [true,  '7.3.0', '^7.1'],
            [true,  '7.3.0', '^5.6 || ^7.0'],
            [true,  '8.1.0', '^7.3 || ~8.0.0 || ~8.1.0'],
            [true,  '8.1.0', '>=7.2.5'],
            [true,  '8.1.0', '^7.3 || ^8.0'],
            [false, '8.1.0', '^7.3 || ~8.0.0'],
            [false, '8.1.0', '^7.2'],
            [false, '8.1.0', '^7.1'],
            [false, '8.1.0', '^5.6 || ^7.0'],
        ];
    }

    #[DataProvider('checks')]
    /** @throws ExpectationFailedException */
    public function test__invoke(bool $expected, string $minimumVersion, string $constraints): void
    {
        $check = new Checker(new VersionParser());
        static::assertSame($expected, $check($minimumVersion, $constraints));
    }
}
