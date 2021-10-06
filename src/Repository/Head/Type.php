<?php

declare(strict_types=1);

namespace PackageInfo\Repository\Head;

class Type
{
    public const BRANCH       = 'branch';
    public const PULL_REQUEST = 'pull-request';
    public const RELEASE      = 'release';
}
