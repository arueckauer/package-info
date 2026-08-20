<?php

declare(strict_types=1);

namespace PackageInfo\Composer\Json;

final readonly class FileReaderFactory
{
    public function __invoke(): FileReader
    {
        return new FileReader();
    }
}
