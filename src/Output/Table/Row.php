<?php

declare(strict_types=1);

namespace PackageInfo\Output\Table;

use PackageInfo\Information\Requirement;
use PackageInfo\Repository\Head;

use function implode;
use function var_export;

class Row
{
    private Requirement $requirement;

    public function __construct(Requirement $requirement)
    {
        $this->requirement = $requirement;
    }

    public function __invoke(string $packageName, Head $head): array
    {
        $requirements            = $this->requirement->parseRequirements($head);
        $developmentRequirements = $this->requirement->parseDevelopmentRequirements($head);

        return [
            'Organization/Repository' => $packageName,
            'Composer Package Name'   => $head->packageName,
            'Head Type'               => $head->headType,
            'Head Name'               => $head->headName,
            'composer.json present'   => var_export($head->composerJsonPresent, true),
            'Requirements'            => implode("\n", $requirements),
            'Dev Requirements'        => implode("\n", $developmentRequirements),
        ];
    }
}
