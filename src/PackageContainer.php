<?php

declare(strict_types=1);

namespace PackageInfo;

use Exception;
use Serializable;

use function array_key_exists;
use function assert;
use function is_string;
use function serialize;
use function unserialize;
use function usort;

class PackageContainer implements Serializable
{
    /** @var Package[] */
    private array $data = [];

    public function __construct(Package ...$packages)
    {
        usort($packages, static function (Package $a, Package $b): int {
            return $a->toString() <=> $b->toString();
        });

        foreach ($packages as $package) {
            $this->add($package);
        }
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

    public function all(): array
    {
        return $this->data;
    }

    public function serialize(): ?string
    {
        return serialize($this->data);
    }

    /**
     * @param string $data
     */
    public function unserialize($data): void
    {
        assert(is_string($data));

        if ('' === $data) {
            return;
        }

        $this->data = unserialize($data, [Package::class]);
    }
}
