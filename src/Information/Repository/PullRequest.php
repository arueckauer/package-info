<?php
declare(strict_types=1);

namespace PackageInfo\Information\Repository;

use PackageInfo\Information\Exception\UnableToSerializeException;
use function get_class_vars;

final class PullRequest
{
    public const COMPOSER_JSON_FILE = 'composer.json';

    public string $organization;

    public string $repository;

    public int $number;

    public string $head;

    private bool $resolved = false;

    /**
     * @var File[]
     */
    public array $files = [];

    public ComposerDetails $composerDetails;

    public function __construct(string $organization, string $repository, int $number, string $head)
    {
        $this->organization = $organization;
        $this->repository = $repository;
        $this->number = $number;
        $this->head = $head;
    }

    public function hasComposerChanges(): bool
    {
        return $this->getComposerJsonFile() !== null;
    }

    public function __sleep()
    {
        if (!$this->resolved) {
            throw UnableToSerializeException::fromUnresolvedPullRequest();
        }

        return array_keys(get_class_vars(__CLASS__));
    }

    public function resolveComposerDetails(): void
    {
        $this->resolved = true;
        $composerJson = $this->getComposerJsonFile();
        if ($composerJson === null) {
            return;
        }

        $this->composerDetails = new ComposerDetails($composerJson->rawUrl);
    }

    private function getComposerJsonFile(): ?File
    {
        foreach ($this->files as $file) {
            if ($file->filename === self::COMPOSER_JSON_FILE) {
                return $file;
            }
        }

        return null;
    }

    /**
     * @param File[] $files
     */
    public function withFiles(array $files): self
    {
        $instance = clone $this;
        $instance->files = $files;
        return $instance;
    }
}
