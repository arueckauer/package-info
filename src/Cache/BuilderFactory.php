<?php

declare(strict_types=1);

namespace PackageInfo\Cache;

use Github\Client;
use PackageInfo\Cache\Branch\Builder as BranchBuilder;
use PackageInfo\Cache\PullRequest\Builder as PullRequestBuilder;
use PackageInfo\Cache\Release\Builder as ReleaseBuilder;
use PackageInfo\Composer\Json\BatchFetcher;
use PackageInfo\Composer\Json\UrlComposer;
use PackageInfo\PackageContainer\Cache;
use PackageInfo\PackageContainer\JsonSerializer;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function rtrim;
use function sprintf;

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
        $serializer = $container->get(JsonSerializer::class);
        assert($serializer instanceof JsonSerializer);

        $cacheFactory = static fn(string $organization): Cache => new Cache(
            sprintf('%s/%s.json', $cacheDirectory, $organization),
            $serializer,
        );

        return new Builder(
            $container->get(Client::class),
            $config['ignore_repositories'],
            $cacheFactory,
            $container->get(BranchBuilder::class),
            $container->get(ReleaseBuilder::class),
            $container->get(PullRequestBuilder::class),
            $container->get(BatchFetcher::class),
            $container->get(UrlComposer::class),
        );
    }
}
