<?php

declare(strict_types=1);

namespace PackageInfo\Composer\Json;

final readonly class UrlComposerFactory
{
    public function __invoke(): UrlComposer
    {
        return new UrlComposer();
    }
}
