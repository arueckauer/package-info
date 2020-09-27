<?php

declare(strict_types=1);

namespace PackageInfo\Information\Repository;

final class File
{
    public string $filename;

    public string $rawUrl;

    public function __construct(string $filename, string $rawUrl)
    {
        $this->filename = $filename;
        $this->rawUrl   = $rawUrl;
    }
}
