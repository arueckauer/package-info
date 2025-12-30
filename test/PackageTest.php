<?php

declare(strict_types=1);

namespace PackageInfoTest;

use PackageInfo\Package;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Package::class)]
final class PackageTest extends TestCase
{
    public function test_toString(): void
    {
        $package = new Package('millennial-falcon', 'hyperdrive', false);

        self::assertSame(
            'millennial-falcon/hyperdrive',
            $package->toString()
        );
    }
}
