<?php

declare(strict_types=1);

namespace PackageInfo;

use Github\Client;
use Psr\Container\ContainerInterface;

class GithubClientFactory
{
    public function __invoke(ContainerInterface $container): Client
    {
        $client = new Client();
        $client->authenticate(
            $container->get('config')['github_api_token'],
            null,
            Client::AUTH_ACCESS_TOKEN
        );

        return $client;
    }
}
