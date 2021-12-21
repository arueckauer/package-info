<?php

declare(strict_types=1);

namespace PackageInfo\Cache;

use Github\Client;
use PackageInfo\Cache\Branch\Builder as BranchBuilder;
use PackageInfo\Cache\PullRequest\Builder as PullRequestBuilder;
use PackageInfo\Cache\Release\Builder as ReleaseBuilder;
use PackageInfo\PackageContainer\Cache;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class BuilderFactory
{
    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container): Builder
    {
        $config = $container->get('config');

        return new Builder(
            $container->get(Client::class),
            $config['ignore_repositories'],
            $container->get(Cache::class),
            $container->get(BranchBuilder::class),
            $container->get(ReleaseBuilder::class),
            $container->get(PullRequestBuilder::class)
        );
    }
}
