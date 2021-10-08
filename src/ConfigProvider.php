<?php

declare(strict_types=1);

namespace PackageInfo;

use Github\Client;
use PackageInfo\Composer\Json\FileReaderFactory;
use PackageInfo\Composer\Json\MetaReaderFactory;
use PackageInfo\Composer\Json\UrlComposerFactory;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                'factories' => [
                    AllCommand::class                  => AllCommandFactory::class,
                    Cache\Branch\Builder::class        => Cache\Branch\BuilderFactory::class,
                    Cache\BuildCommand::class          => Cache\BuildCommandFactory::class,
                    Cache\Builder::class               => Cache\BuilderFactory::class,
                    Cache\PullRequest\Builder::class   => Cache\PullRequest\BuilderFactory::class,
                    Cache\Release\Builder::class       => Cache\Release\BuilderFactory::class,
                    Client::class                      => GithubClientFactory::class,
                    Composer\Json\FileReader::class    => FileReaderFactory::class,
                    Composer\Json\MetaReader::class    => MetaReaderFactory::class,
                    Composer\Json\UrlComposer::class   => UrlComposerFactory::class,
                    GetCommand::class                  => GetCommandFactory::class,
                    Output\Table\Row::class            => Output\Table\RowFactory::class,
                    PackageContainer::class            => PackageContainerFactory::class,
                    PackageContainer\Cache::class      => PackageContainer\CacheFactory::class,
                    Requirement\Checker::class         => Requirement\CheckerFactory::class,
                    Requirement\Renderer::class        => Requirement\RendererFactory::class,
                    Requirement\Version\Checker::class => Requirement\Version\CheckerFactory::class,
                ],
            ],
        ];
    }
}
