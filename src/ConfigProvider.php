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
use PackageInfo\Information\Requirement;
use PackageInfo\Information\RequirementFactory;
use PackageInfo\Output\Table\Row;
use PackageInfo\Output\Table\RowFactory;
use PackageInfo\PackageContainer\Cache;
use PackageInfo\PackageContainer\CacheFactory;
use PackageInfo\Requirement\Version\Check;
use PackageInfo\Requirement\Version\CheckFactory;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                'factories' => [
                    Cache::class                  => CacheFactory::class,
                    Check::class                  => CheckFactory::class,
                    Client::class                 => GithubClientFactory::class,
                    CacheBuildCommand::class      => CacheBuildCommandFactory::class,
                    PackageContainer::class       => PackageContainerFactory::class,
                    PackageInfoGetCommand::class  => PackageInfoGetCommandFactory::class,
                    PackageInfoListCommand::class => PackageInfoListCommandFactory::class,
                    Requirement::class            => RequirementFactory::class,
                    Row::class                    => RowFactory::class,
                ],
            ],
        ];
    }
}
