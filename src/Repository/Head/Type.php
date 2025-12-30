<?php

declare(strict_types=1);

namespace PackageInfo\Repository\Head;

enum Type: string
{
    case Branch      = 'branch';
    case PullRequest = 'pull-request';
    case Release     = 'release';
}
