<?php

declare(strict_types=1);

namespace PackageInfoTest;

use Github\Client;
use PackageInfo\Cache\Branch\Builder as BranchBuilder;
use PackageInfo\Cache\BuildCommand;
use PackageInfo\Cache\Builder;
use PackageInfo\Cache\PullRequest\Builder as PullRequestBuilder;
use PackageInfo\Cache\Release\Builder as ReleaseBuilder;
use PackageInfo\CheckCommand;
use PackageInfo\Composer\Json\FileReader as ComposerJsonFileReader;
use PackageInfo\Composer\Json\MetaReader as ComposerJsonMetaReader;
use PackageInfo\Composer\Json\UrlComposer as ComposerJsonUrlComposer;
use PackageInfo\ConfigProvider;
use PackageInfo\GetCommand;
use PackageInfo\Output\Table\Row;
use PackageInfo\PackageContainer;
use PackageInfo\PackageContainer\Cache;
use PackageInfo\Requirement\Checker;
use PackageInfo\Requirement\Version\Checker as VersionChecker;
use PHPUnit\Framework\TestCase;

class ConfigProviderTest extends TestCase
{
    /**
     * @covers \PackageInfo\ConfigProvider
     */
    public function testInvoke(): void
    {
        $configProvider = new ConfigProvider();
        $config         = $configProvider();

        self::assertArrayHasKey('dependencies', $config);
        self::assertIsArray($config['dependencies']);
        self::assertArrayHasKey('factories', $config['dependencies']);
        self::assertIsArray($config['dependencies']['factories']);
        self::assertCount(16, $config['dependencies']['factories']);
        self::assertArrayHasKey(BranchBuilder::class, $config['dependencies']['factories']);
        self::assertArrayHasKey(BuildCommand::class, $config['dependencies']['factories']);
        self::assertArrayHasKey(Builder::class, $config['dependencies']['factories']);
        self::assertArrayHasKey(Cache::class, $config['dependencies']['factories']);
        self::assertArrayHasKey(CheckCommand::class, $config['dependencies']['factories']);
        self::assertArrayHasKey(Checker::class, $config['dependencies']['factories']);
        self::assertArrayHasKey(Client::class, $config['dependencies']['factories']);
        self::assertArrayHasKey(ComposerJsonFileReader::class, $config['dependencies']['factories']);
        self::assertArrayHasKey(ComposerJsonMetaReader::class, $config['dependencies']['factories']);
        self::assertArrayHasKey(ComposerJsonUrlComposer::class, $config['dependencies']['factories']);
        self::assertArrayHasKey(GetCommand::class, $config['dependencies']['factories']);
        self::assertArrayHasKey(PackageContainer::class, $config['dependencies']['factories']);
        self::assertArrayHasKey(PullRequestBuilder::class, $config['dependencies']['factories']);
        self::assertArrayHasKey(ReleaseBuilder::class, $config['dependencies']['factories']);
        self::assertArrayHasKey(Row::class, $config['dependencies']['factories']);
        self::assertArrayHasKey(VersionChecker::class, $config['dependencies']['factories']);
    }
}
