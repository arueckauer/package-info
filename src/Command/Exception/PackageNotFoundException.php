<?php

declare(strict_types=1);

namespace PackageInfo\Command\Exception;

use RuntimeException;

use function sprintf;

class PackageNotFoundException extends RuntimeException
{
    public static function byPackage(string $package): self
    {
        return new self(
            sprintf(
                "Package (%s) could not be found.",
                $package
            )
        );
    }
}
