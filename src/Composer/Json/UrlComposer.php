<?php

declare(strict_types=1);

namespace PackageInfo\Composer\Json;

use function sprintf;

final readonly class UrlComposer
{
    public function __invoke(string $owner, string $repository, string $head): string
    {
        return sprintf(
            'https://raw.githubusercontent.com/%s/%s/%s/composer.json',
            $owner,
            $repository,
            $head
        );
    }
}
