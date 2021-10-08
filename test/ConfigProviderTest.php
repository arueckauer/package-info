<?php

declare(strict_types=1);

namespace PackageInfoTest;

use Github\Client;
use PackageInfo\Command\CacheBuildCommand;
use PackageInfo\Command\PackageInfoGetCommand;
use PackageInfo\Command\PackageInfoListCommand;
use PackageInfo\ConfigProvider;
use PackageInfo\Output\Table\Row;
use PackageInfo\PackageContainer;
use PackageInfo\PackageContainer\Cache;
use PackageInfo\Requirement\Checker;
use PackageInfo\Requirement\Renderer;
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

        $this->assertArrayHasKey('dependencies', $config);
        $this->assertIsArray($config['dependencies']);
        $this->assertArrayHasKey('factories', $config['dependencies']);
        $this->assertIsArray($config['dependencies']['factories']);
        $this->assertArrayHasKey(Cache::class, $config['dependencies']['factories']);
        $this->assertArrayHasKey(Checker::class, $config['dependencies']['factories']);
        $this->assertArrayHasKey(Renderer::class, $config['dependencies']['factories']);
        $this->assertArrayHasKey(Client::class, $config['dependencies']['factories']);
        $this->assertArrayHasKey(CacheBuildCommand::class, $config['dependencies']['factories']);
        $this->assertArrayHasKey(PackageContainer::class, $config['dependencies']['factories']);
        $this->assertArrayHasKey(PackageInfoGetCommand::class, $config['dependencies']['factories']);
        $this->assertArrayHasKey(PackageInfoListCommand::class, $config['dependencies']['factories']);
        $this->assertArrayHasKey(VersionChecker::class, $config['dependencies']['factories']);
        $this->assertArrayHasKey(Row::class, $config['dependencies']['factories']);
    }
}
