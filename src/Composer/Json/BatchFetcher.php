<?php

declare(strict_types=1);

namespace PackageInfo\Composer\Json;

use GuzzleHttp\Client;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use JsonException;
use Psr\Http\Message\ResponseInterface;

use function is_array;
use function json_decode;

use const JSON_THROW_ON_ERROR;

final readonly class BatchFetcher
{
    public function __construct(
        private Client $client,
        private int $concurrency = 20,
    ) {}

    /**
     * @param array<string, string> $urls key => URL
     * @return array<string, array>       key => parsed composer.json (empty array on error)
     */
    public function fetch(array $urls): array
    {
        if ($urls === []) {
            return [];
        }

        $results = [];

        $requests = (static function () use ($urls) {
            foreach ($urls as $key => $url) {
                yield $key => new Request('GET', $url);
            }
        })();

        $pool = new Pool($this->client, $requests, [
            'concurrency' => $this->concurrency,
            'fulfilled' => static function (ResponseInterface $response, string $key) use (&$results): void {
                try {
                    $decoded = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);
                    $results[$key] = is_array($decoded) ? $decoded : [];
                } catch (JsonException) {
                    $results[$key] = [];
                }
            },
            'rejected' => static function (mixed $reason, string $key) use (&$results): void {
                $results[$key] = [];
            },
        ]);

        $pool->promise()->wait();

        return $results;
    }
}
