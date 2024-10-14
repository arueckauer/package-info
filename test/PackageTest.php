<?php

declare(strict_types=1);

namespace PackageInfoTest;

use PackageInfo\Package;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Package::class)]
class PackageTest extends TestCase
{
    public function test_toString(): void
    {
        $package = new Package('millennial-falcon', 'hyperdrive');

        self::assertSame(
            'millennial-falcon/hyperdrive',
            $package->toString()
        );
    }
}
