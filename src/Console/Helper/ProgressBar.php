<?php

declare(strict_types=1);

namespace PackageInfo\Console\Helper;

final class ProgressBar
{
    public const string FORMAT_MAIN = 'format_main';
    public const string FORMAT_BRANCHES = 'format_branches';
    public const string FORMAT_RELEASES = 'format_releases';
    public const string FORMAT_PULL_REQUESTS = 'format_pull_requests';

    public static array $formats = [
        self::FORMAT_MAIN => ' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% <comment>Repository</comment> <info>%message%</info>',
        self::FORMAT_BRANCHES => ' %current%/%max% [%bar%] %percent:3s%% <comment>Branch</comment> <info>%message%</info>',
        self::FORMAT_RELEASES => ' %current%/%max% [%bar%] %percent:3s%% <comment>Release</comment> <info>%message%</info>',
        self::FORMAT_PULL_REQUESTS => ' %current%/%max% [%bar%] %percent:3s%% <comment>Pull Request</comment> <info>%message%</info>',
    ];
}
