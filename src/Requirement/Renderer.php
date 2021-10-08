<?php

declare(strict_types=1);

namespace PackageInfo\Requirement;

use PackageInfo\Requirement\Check\Result;

use function sprintf;

class Renderer
{
    private const SUCCESS_FORMAT = 'info';
    private const FAILURE_FORMAT = 'comment';

    public function __invoke(Result $result): string
    {
        if (! $result->hasRequirement) {
            return sprintf(
                '%s: <error>n/a</error>',
                $result->requirementName
            );
        }

        return sprintf(
            '%1$s: <%3$s>%2$s</%3$s>',
            $result->requirementName,
            $result->versionConstraint,
            $result->isSupported ? self::SUCCESS_FORMAT : self::FAILURE_FORMAT
        );
    }
}
