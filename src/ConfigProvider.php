<?php

declare(strict_types=1);

namespace PackageInfo;

use Github\Client;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                'factories' => [
                    Client::class                         => GithubClientFactory::class,
                    Command\CacheBuildCommand::class      => Command\CacheBuildCommandFactory::class,
                    Command\PackageInfoGetCommand::class  => Command\PackageInfoGetCommandFactory::class,
                    Command\PackageInfoListCommand::class => Command\PackageInfoListCommandFactory::class,
                    Output\Table\Row::class               => Output\Table\RowFactory::class,
                    PackageContainer::class               => PackageContainerFactory::class,
                    PackageContainer\Cache::class         => PackageContainer\CacheFactory::class,
                    Requirement\Checker::class            => Requirement\CheckerFactory::class,
                    Requirement\Renderer::class           => Requirement\RendererFactory::class,
                    Requirement\Version\Checker::class    => Requirement\Version\CheckerFactory::class,
                ],
            ],
        ];
    }
}
