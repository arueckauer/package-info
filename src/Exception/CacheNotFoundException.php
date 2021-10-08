<?php

declare(strict_types=1);

namespace PackageInfo\Exception;

use RuntimeException;

use function sprintf;

class CacheNotFoundException extends RuntimeException
{
    public static function byFilename(string $filename): self
    {
        return new self(
            sprintf(
                "Cache file (%s) could not be found.",
                $filename
            )
        );
    }
}
