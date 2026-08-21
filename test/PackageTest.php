<?php

declare(strict_types=1);

namespace PackageInfoTest;

use PackageInfo\Package;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

#[CoversClass(Package::class)]
final class PackageTest extends TestCase
{
    /** @throws ExpectationFailedException */
    public function test_toString(): void
    {
        $package = new Package('millennial-falcon', 'hyperdrive', false);

        static::assertSame('millennial-falcon/hyperdrive', $package->toString());
    }
}
