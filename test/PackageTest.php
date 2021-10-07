<?php

declare(strict_types=1);

namespace PackageInfoTest;

use PackageInfo\Package;
use PHPUnit\Framework\TestCase;

class PackageTest extends TestCase
{
    /**
     * @covers \PackageInfo\Package::toString
     */
    public function test_toString(): void
    {
        $package = new Package('millennial-falcon', 'hyperdrive');

        self::assertSame(
            'millennial-falcon/hyperdrive',
            $package->toString()
        );
    }
}
