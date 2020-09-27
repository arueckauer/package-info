<?php

declare(strict_types=1);

namespace PackageInfo\Information\Repository;

use function sprintf;

class Branch
{
    private string $name;
    public ComposerDetails $composerDetails;

    public function __construct(
        string $username,
        string $repository,
        string $name
    ) {
        $composerJsonPath = sprintf(
            'https://raw.githubusercontent.com/%s/%s/%s/composer.json',
            $username,
            $repository,
            $name
        );

        $this->composerDetails = new ComposerDetails($composerJsonPath);
        $this->setName($name);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
