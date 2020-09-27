<?php
declare(strict_types=1);

namespace PackageInfo\Information\Exception;

use RuntimeException;

final class UnableToSerializeException extends RuntimeException
{
    public static function fromUnresolvedPullRequest(): self
    {
        return new self('Cannot serialize unresolved pull request instance.');
    }
}
