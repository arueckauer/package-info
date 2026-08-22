<?php

declare(strict_types=1);

namespace PackageInfo;

use Exception;
use Override;
use PackageInfo\Repository\Head;
use Serializable;

use function array_key_exists;
use function serialize;
use function unserialize;
use function usort;

final class PackageContainer implements Serializable
{
    /** @var array<string, Package> */
    private array $data = [];

    public function __construct(Package ...$packages)
    {
        usort($packages, static fn(Package $a, Package $b): int => $a->toString() <=> $b->toString());

        foreach ($packages as $package) {
            $this->add($package);
        }
    }

    public static function withPackages(Package ...$packages): self
    {
        return new self(...$packages);
    }

    public static function merge(self ...$containers): self
    {
        $all = [];
        foreach ($containers as $container) {
            foreach ($container->all() as $package) {
                $all[] = $package;
            }
        }

        return new self(...$all);
    }

    /**
     * @throws Exception
     */
    public function __serialize(): array
    {
        return ['data' => $this->serialize()];
    }

    public function __unserialize(array $data): void
    {
        $this->unserialize($data[0]);
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->data);
    }

    public function get(string $name): Package
    {
        return $this->data[$name];
    }

    public function add(Package $package): void
    {
        $this->data[$package->toString()] = $package;
    }

    /** @return array<string, Package> */
    public function all(): array
    {
        return $this->data;
    }

    #[Override]
    public function serialize(): ?string
    {
        return serialize($this->data);
    }

    #[Override]
    public function unserialize($data): void
    {
        if ('' === $data) {
            return;
        }

        $this->data = unserialize($data, ['allowed_classes' => [Package::class, Head::class]]);
    }
}
