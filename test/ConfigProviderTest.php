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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\GeneratorNotSupportedException;
use PHPUnit\Framework\TestCase;

#[CoversClass(ConfigProvider::class)]
final class ConfigProviderTest extends TestCase
{
    /**
     * @throws ExpectationFailedException
     * @throws Exception
     * @throws GeneratorNotSupportedException
     */
    public function testInvoke(): void
    {
        $configProvider = new ConfigProvider();
        $config = $configProvider();

        static::assertArrayHasKey('dependencies', $config);
        static::assertIsArray($config['dependencies']);
        static::assertArrayHasKey('factories', $config['dependencies']);
        static::assertIsArray($config['dependencies']['factories']);
        static::assertCount(16, $config['dependencies']['factories']);
        static::assertArrayHasKey(BranchBuilder::class, $config['dependencies']['factories']);
        static::assertArrayHasKey(BuildCommand::class, $config['dependencies']['factories']);
        static::assertArrayHasKey(Builder::class, $config['dependencies']['factories']);
        static::assertArrayHasKey(Cache::class, $config['dependencies']['factories']);
        static::assertArrayHasKey(CheckCommand::class, $config['dependencies']['factories']);
        static::assertArrayHasKey(Checker::class, $config['dependencies']['factories']);
        static::assertArrayHasKey(Client::class, $config['dependencies']['factories']);
        static::assertArrayHasKey(ComposerJsonFileReader::class, $config['dependencies']['factories']);
        static::assertArrayHasKey(ComposerJsonMetaReader::class, $config['dependencies']['factories']);
        static::assertArrayHasKey(ComposerJsonUrlComposer::class, $config['dependencies']['factories']);
        static::assertArrayHasKey(GetCommand::class, $config['dependencies']['factories']);
        static::assertArrayHasKey(PackageContainer::class, $config['dependencies']['factories']);
        static::assertArrayHasKey(PullRequestBuilder::class, $config['dependencies']['factories']);
        static::assertArrayHasKey(ReleaseBuilder::class, $config['dependencies']['factories']);
        static::assertArrayHasKey(Row::class, $config['dependencies']['factories']);
        static::assertArrayHasKey(VersionChecker::class, $config['dependencies']['factories']);
    }
}
