<?php

declare(strict_types=1);

namespace PackageInfo\Output\Table;

use PackageInfo\Repository\Head;

use function var_export;

class Row
{
    public function __invoke(string $packageName, Head $head): array
    {
        return [
            'Organization/Repository' => $packageName,
            'Composer Package Name'   => $head->packageName,
            'Head Type'               => $head->headType,
            'Head Name'               => $head->headName,
            'composer.json present'   => var_export($head->composerJsonPresent, true),
        ];
    }
}
