<?php

declare(strict_types=1);

namespace PackageInfo\Cache;

use Exception;
use Override;
use PackageInfo\Console\Helper\ProgressBar;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar as SymfonyProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function assert;

final class BuildCommand extends Command
{
    public function __construct(
        private readonly array $organizations,
        private readonly Builder $builder,
    ) {
        parent::__construct();
    }

    #[Override]
    public function configure(): void
    {
        $this->setName('cache:build');
        $this->setDescription('Caches package information for repositories of configured organization(s)');
    }

    /**
     * @throws Exception
     */
    #[Override]
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        assert($output instanceof ConsoleOutputInterface);

        foreach (ProgressBar::$formats as $name => $format) {
            SymfonyProgressBar::setFormatDefinition($name, $format);
        }

        foreach ($this->organizations as $organization) {
            ($this->builder)($output, $organization);
        }

        return 0;
    }
}
