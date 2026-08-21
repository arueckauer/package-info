<?php

declare(strict_types=1);

namespace PackageInfo\PackageContainer;

use DateTimeImmutable;
use PackageInfo\Package;
use PackageInfo\PackageContainer;
use PackageInfo\Repository\Head;
use PackageInfo\Repository\Head\Type;

use function array_map;
use function array_values;
use function assert;
use function is_array;
use function json_decode;
use function json_encode;

use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;

final class JsonSerializer
{
    public function serialize(PackageContainer $packageContainer, string $organization): string
    {
        $repositories = array_map(
            static fn(Package $package): array => [
                'name' => $package->toString(),
                'organization' => $package->organization,
                'repository' => $package->repository,
                'is_archived' => $package->isArchived,
                'heads' => array_map(
                    static fn(Head $head): array => [
                        'type' => $head->headType,
                        'name' => $head->headName,
                        'package_name' => $head->packageName,
                        'composer_json_present' => $head->composerJsonPresent,
                        'require' => $head->requirements,
                        'require_dev' => $head->developmentRequirements,
                    ],
                    $package->heads,
                ),
            ],
            $packageContainer->all(),
        );

        return json_encode(
            [
                'organization' => $organization,
                'generated_at' => new DateTimeImmutable()->format(DateTimeImmutable::ATOM),
                'repositories' => array_values($repositories),
            ],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR,
        );
    }

    public function deserialize(string $json): PackageContainer
    {
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        assert(is_array($data));

        $packageContainer = new PackageContainer();

        $repositories = $data['repositories'] ?? [];
        assert(is_array($repositories));

        foreach ($repositories as $repoData) {
            assert(is_array($repoData));

            $org = (string) ($repoData['organization'] ?? '');
            $repo = (string) ($repoData['repository'] ?? '');

            if ($org === '' || $repo === '') {
                continue;
            }

            $heads = [];
            foreach ($repoData['heads'] ?? [] as $headData) {
                assert(is_array($headData));

                $heads[] = new Head(
                    (string) ($headData['package_name'] ?? ''),
                    (string) ($headData['type'] ?? Type::Branch->value),
                    (string) ($headData['name'] ?? ''),
                    (bool) ($headData['composer_json_present'] ?? false),
                    (array) ($headData['require'] ?? []),
                    (array) ($headData['require_dev'] ?? []),
                );
            }

            $packageContainer->add(new Package($org, $repo, (bool) ($repoData['is_archived'] ?? false), ...$heads));
        }

        return $packageContainer;
    }
}
