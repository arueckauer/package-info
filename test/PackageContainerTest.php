<?php

declare(strict_types=1);

namespace PackageInfoTest;

use PackageInfo\Package;
use PackageInfo\PackageContainer;
use PHPUnit\Framework\TestCase;

class PackageContainerTest extends TestCase
{
    /**
     * @covers \PackageInfo\PackageContainer::has
     */
    public function test_has(): void
    {
        $package   = new Package('millennial-falcon', 'hyperdrive');
        $container = new PackageContainer($package);

        self::assertTrue($container->has('millennial-falcon/hyperdrive'));
        self::assertFalse($container->has('tie-fighter/hyperdrive'));
    }

    /**
     * @covers \PackageInfo\PackageContainer::get
     */
    public function test_get(): void
    {
        $package   = new Package('millennial-falcon', 'hyperdrive');
        $container = new PackageContainer($package);

        self::assertSame(
            $package,
            $container->get('millennial-falcon/hyperdrive')
        );
    }

    /**
     * @covers \PackageInfo\PackageContainer::get
     */
    public function test_add(): void
    {
        $package   = new Package('millennial-falcon', 'hyperdrive');
        $container = new PackageContainer();
        $container->add($package);

        self::assertSame(
            $package,
            $container->get('millennial-falcon/hyperdrive')
        );
    }

    /**
     * @covers \PackageInfo\PackageContainer::all
     */
    public function test_all(): void
    {
        $package   = new Package('millennial-falcon', 'hyperdrive');
        $container = new PackageContainer($package);

        self::assertSame(
            ['millennial-falcon/hyperdrive' => $package],
            $container->all()
        );
    }

    /**
     * @covers \PackageInfo\PackageContainer::all
     */
    public function test_all_is_sorted_by_name(): void
    {
        $packageA = new Package('millennial-falcon', 'hyperdrive');
        $packageB = new Package('x-wing', 'hyperdrive');
        $packageC = new Package('b-wing', 'hyperdrive');

        $expected = [
            'b-wing/hyperdrive'            => $packageC,
            'millennial-falcon/hyperdrive' => $packageA,
            'x-wing/hyperdrive'            => $packageB,
        ];

        $container = new PackageContainer(
            $packageA,
            $packageB,
            $packageC
        );

        self::assertSame(
            $expected,
            $container->all()
        );
    }

    /**
     * @covers \PackageInfo\PackageContainer::serialize
     * @covers \PackageInfo\PackageContainer::unserialize
     */
    public function test_serialize_and_unserialize(): void
    {
        $containerA = new PackageContainer(
            new Package('millennial-falcon', 'hyperdrive'),
            new Package('x-wing', 'hyperdrive'),
            new Package('b-wing', 'hyperdrive')
        );

        $containerB = new PackageContainer();
        $containerB->unserialize((string) $containerA->serialize());

        self::assertEquals(
            $containerA,
            $containerB
        );
    }
}
