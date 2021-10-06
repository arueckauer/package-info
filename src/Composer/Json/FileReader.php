<?php

declare(strict_types=1);

namespace PackageInfo\Composer\Json;

use JsonException;

use function file_get_contents;
use function json_decode;

use const JSON_THROW_ON_ERROR;

class FileReader
{
    public function __invoke(string $composerJsonPath): array
    {
        $composerJson = @file_get_contents($composerJsonPath);
        if (false !== $composerJson) {
            try {
                return json_decode($composerJson, true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $exception) {
            }
        }

        return [];
    }
}
