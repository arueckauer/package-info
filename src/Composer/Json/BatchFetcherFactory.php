<?php

declare(strict_types=1);

namespace PackageInfo\Composer\Json;

use GuzzleHttp\Client;

final readonly class BatchFetcherFactory
{
    public function __invoke(): BatchFetcher
    {
        return new BatchFetcher(new Client());
    }
}
