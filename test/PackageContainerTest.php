<?php

declare(strict_types=1);

namespace PackageInfoTest;

use Exception;
use PackageInfo\Package;
use PackageInfo\PackageContainer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

#[CoversClass(PackageContainer::class)]
final class PackageContainerTest extends TestCase
{
    /** @throws ExpectationFailedException */
    public function test_has(): void
    {
        $package = new Package('millennial-falcon', 'hyperdrive', false);
        $container = new PackageContainer($package);

        static::assertTrue($container->has('millennial-falcon/hyperdrive'));
        static::assertFalse($container->has('tie-fighter/hyperdrive'));
    }

    /** @throws ExpectationFailedException */
    public function test_get(): void
    {
        $package = new Package('millennial-falcon', 'hyperdrive', false);
        $container = new PackageContainer($package);

        static::assertSame($package, $container->get('millennial-falcon/hyperdrive'));
    }

    /** @throws ExpectationFailedException */
    public function test_add(): void
    {
        $package = new Package('millennial-falcon', 'hyperdrive', false);
        $container = new PackageContainer();
        $container->add($package);

        static::assertSame($package, $container->get('millennial-falcon/hyperdrive'));
    }

    /** @throws ExpectationFailedException */
    public function test_all(): void
    {
        $package = new Package('millennial-falcon', 'hyperdrive', false);
        $container = new PackageContainer($package);

        static::assertSame(['millennial-falcon/hyperdrive' => $package], $container->all());
    }

    /** @throws ExpectationFailedException */
    public function test_all_is_sorted_by_name(): void
    {
        $packageA = new Package('millennial-falcon', 'hyperdrive', false);
        $packageB = new Package('x-wing', 'hyperdrive', false);
        $packageC = new Package('b-wing', 'hyperdrive', false);

        $expected = [
            'b-wing/hyperdrive' => $packageC,
            'millennial-falcon/hyperdrive' => $packageA,
            'x-wing/hyperdrive' => $packageB,
        ];

        $container = new PackageContainer($packageA, $packageB, $packageC);

        static::assertSame($expected, $container->all());
    }

    /**
     * @throws Exception
     */
    public function test_serialize_and_unserialize(): void
    {
        $containerA = new PackageContainer(
            new Package('millennial-falcon', 'hyperdrive', false),
            new Package('x-wing', 'hyperdrive', false),
            new Package('b-wing', 'hyperdrive', false),
        );

        $containerB = new PackageContainer();
        $containerB->unserialize((string) $containerA->serialize());

        static::assertEquals($containerA, $containerB);
    }
}
