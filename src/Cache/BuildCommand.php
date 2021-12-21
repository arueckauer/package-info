<?php

declare(strict_types=1);

namespace PackageInfo\Cache;

use Exception;
use PackageInfo\Console\Helper\ProgressBar;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar as SymfonyProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function assert;

class BuildCommand extends Command
{
    private Builder $builder;
    private array $organizations;

    public function __construct(array $organizations, Builder $builder)
    {
        $this->builder       = $builder;
        $this->organizations = $organizations;

        parent::__construct();
    }

    public function configure(): void
    {
        $this->setName('cache:build');
        $this->setDescription('Caches package information for repositories of configured organization(s)');
    }

    /**
     * @throws Exception
     */
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
