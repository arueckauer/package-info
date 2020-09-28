<?php

declare(strict_types=1);

namespace PackageInfo;

use Github\Client;
use PackageInfo\Command\CacheBuildCommand;
use PackageInfo\Command\CacheBuildCommandFactory;
use PackageInfo\Command\PackageInfoGetCommand;
use PackageInfo\Command\PackageInfoGetCommandFactory;
use PackageInfo\Command\PackageInfoListCommand;
use PackageInfo\Command\PackageInfoListCommandFactory;
use PackageInfo\Command\PackageInfoListNonMatchingRequirementsCommand;
use PackageInfo\Command\PackageInfoListNonMatchingRequirementsCommandFactory;
use PackageInfo\Information\Requirement;
use PackageInfo\Information\RequirementFactory;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                'factories' => [
                    Client::class                 => GithubClientFactory::class,
                    CacheBuildCommand::class      => CacheBuildCommandFactory::class,
                    PackageInfoGetCommand::class  => PackageInfoGetCommandFactory::class,
                    PackageInfoListCommand::class => PackageInfoListCommandFactory::class,
                    PackageInfoListNonMatchingRequirementsCommand::class
                        => PackageInfoListNonMatchingRequirementsCommandFactory::class,
                    Requirement::class => RequirementFactory::class,
                ],
            ],
        ];
    }
}
