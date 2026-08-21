<?php

declare(strict_types=1);

namespace PackageInfo\Composer\Json;

final readonly class MetaReaderFactory
{
    public function __invoke(): MetaReader
    {
        return new MetaReader();
    }
}
