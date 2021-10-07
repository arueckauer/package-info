<?php

declare(strict_types=1);

namespace PackageInfo\PackageContainer\Exception;

use RuntimeException;

use function sprintf;

class CacheFileNotWritableException extends RuntimeException
{
    public static function fromFilename(string $filename): self
    {
        return new self(sprintf('Cache file "%s" is not writable.', $filename));
    }
}
