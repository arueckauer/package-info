<?php

declare(strict_types=1);

namespace PackageInfo\Console\Helper;

final class ProgressBar
{
    public const FORMAT_MAIN          = 'format_main';
    public const FORMAT_BRANCHES      = 'format_branches';
    public const FORMAT_RELEASES      = 'format_releases';
    public const FORMAT_PULL_REQUESTS = 'format_pull_requests';

    // phpcs:disable
    public static array $formats = [
        self::FORMAT_MAIN          => ' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% <comment>Repository</comment> <info>%message%</info>',
        self::FORMAT_BRANCHES      => ' %current%/%max% [%bar%] %percent:3s%% <comment>Branch</comment> <info>%message%</info>',
        self::FORMAT_RELEASES      => ' %current%/%max% [%bar%] %percent:3s%% <comment>Release</comment> <info>%message%</info>',
        self::FORMAT_PULL_REQUESTS => ' %current%/%max% [%bar%] %percent:3s%% <comment>Pull Request</comment> <info>%message%</info>',
    ];
    // phpcs:enable
}
