<?php

declare(strict_types=1);

namespace PackageInfo\Cache;

use Github\Client;
use PackageInfo\Cache\Branch\Builder as BranchBuilder;
use PackageInfo\Cache\PullRequest\Builder as PullRequestBuilder;
use PackageInfo\Cache\Release\Builder as ReleaseBuilder;
use PackageInfo\Composer\Json\BatchFetcher;
use PackageInfo\Composer\Json\UrlComposer;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function rtrim;

final readonly class BuilderFactory
{
    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container): Builder
    {
        $config = $container->get('config');
        $cacheDirectory = rtrim((string) ($config['cache_directory'] ?? ''), '/\\');

        return new Builder(
            $container->get(Client::class),
            $config['ignore_repositories'],
            $cacheDirectory,
            $container->get(BranchBuilder::class),
            $container->get(ReleaseBuilder::class),
            $container->get(PullRequestBuilder::class),
            $container->get(BatchFetcher::class),
            $container->get(UrlComposer::class),
        );
    }
}
