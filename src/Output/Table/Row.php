<?php

declare(strict_types=1);

namespace PackageInfo\Output\Table;

use PackageInfo\Repository\Head;
use PackageInfo\Requirement\Checker;
use PackageInfo\Requirement\Renderer;

use function implode;
use function var_export;

class Row
{
    private Checker $checker;
    private Renderer $renderer;

    public function __construct(Checker $checker, Renderer $renderer)
    {
        $this->checker  = $checker;
        $this->renderer = $renderer;
    }

    public function __invoke(string $packageName, Head $head): array
    {
        $requirements = [];
        foreach ($this->checker->checkRequirements($head) as $result) {
            $requirements[] = ($this->renderer)($result);
        }

        $developmentRequirements = [];
        foreach ($this->checker->checkDevelopmentRequirements($head) as $result) {
            $developmentRequirements[] = ($this->renderer)($result);
        }

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
