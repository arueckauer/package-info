<?php

declare(strict_types=1);

namespace PackageInfoTest\Repository;

use PackageInfo\Repository\Head;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Head::class)]
final class HeadTest extends TestCase
{
    public function test_hasRequirement(): void
    {
        $head = new Head('', '', '', false, ['millennial-falcon/hyperdrive' => ''], []);

        static::assertTrue($head->hasRequirement('millennial-falcon/hyperdrive'));
        static::assertFalse($head->hasRequirement('b-wing/hyperdrive'));
    }

    public function test_getVersionConstraintOfRequirement(): void
    {
        $head = new Head('', '', '', false, ['millennial-falcon/hyperdrive' => '^1.0'], []);

        static::assertSame('^1.0', $head->getVersionConstraintOfRequirement('millennial-falcon/hyperdrive'));
    }

    public function test_hasDevelopmentRequirement(): void
    {
        $head = new Head('', '', '', false, ['starship/builder' => ''], []);

        static::assertTrue($head->hasRequirement('starship/builder'));
        static::assertFalse($head->hasRequirement('droid/builder'));
    }

    public function test_getVersionConstraintOfDevelopmentRequirement(): void
    {
        $head = new Head('', '', '', false, ['starship/builder' => '^7.5'], []);

        static::assertSame('^7.5', $head->getVersionConstraintOfRequirement('starship/builder'));
    }
}
