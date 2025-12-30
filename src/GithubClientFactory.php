<?php

declare(strict_types=1);

namespace PackageInfo;

use Github\AuthMethod;
use Github\Client;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final readonly class GithubClientFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): Client
    {
        $client = new Client();
        $client->authenticate(
            $container->get('config')['github_api_token'],
            null,
            AuthMethod::ACCESS_TOKEN
        );

        return $client;
    }
}
