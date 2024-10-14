<?php

declare(strict_types=1);

namespace PackageInfoTest\Repository;

use PackageInfo\Repository\Head;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Head::class)]
class HeadTest extends TestCase
{
    public function test_hasRequirement(): void
    {
        $head                                               = new Head();
        $head->requirements['millennial-falcon/hyperdrive'] = '';

        self::assertTrue($head->hasRequirement('millennial-falcon/hyperdrive'));
        self::assertFalse($head->hasRequirement('b-wing/hyperdrive'));
    }

    public function test_getVersionConstraintOfRequirement(): void
    {
        $head                                               = new Head();
        $head->requirements['millennial-falcon/hyperdrive'] = '^1.0';

        self::assertSame('^1.0', $head->getVersionConstraintOfRequirement('millennial-falcon/hyperdrive'));
    }

    public function test_hasDevelopmentRequirement(): void
    {
        $head                                   = new Head();
        $head->requirements['starship/builder'] = '';

        self::assertTrue($head->hasRequirement('starship/builder'));
        self::assertFalse($head->hasRequirement('droid/builder'));
    }

    public function test_getVersionConstraintOfDevelopmentRequirement(): void
    {
        $head                                   = new Head();
        $head->requirements['starship/builder'] = '^7.5';

        self::assertSame('^7.5', $head->getVersionConstraintOfRequirement('starship/builder'));
    }
}
