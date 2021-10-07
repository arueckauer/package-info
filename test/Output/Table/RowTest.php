<?php

declare(strict_types=1);

namespace PackageInfoTest\Output\Table;

use PackageInfo\Information\Requirement;
use PackageInfo\Output\Table\Row;
use PackageInfo\Repository\Head;
use PHPUnit\Framework\TestCase;

class RowTest extends TestCase
{
    /**
     * @covers \PackageInfo\Output\Table\Row::__invoke
     */
    public function test___invoke(): void
    {
        $expected = [
            'Organization/Repository' => 'millennial-falcon-ship/the-ship',
            'Composer Package Name'   => 'millennial-falcon/ship',
            'Head Type'               => 'branch',
            'Head Name'               => 'main',
            'composer.json present'   => 'true',
            'Requirements'            => 'millennial-falcon/hyperdrive:^1.0',
            'Dev Requirements'        => "starship/builder:^7.5\ndroid/build:^9.8",
        ];

        $requirement = $this->createMock(Requirement::class);

        $requirement
            ->expects(self::once())
            ->method('parseRequirements')
            ->willReturn(['millennial-falcon/hyperdrive:^1.0']);

        $requirement
            ->expects(self::once())
            ->method('parseDevelopmentRequirements')
            ->willReturn(['starship/builder:^7.5', 'droid/build:^9.8']);

        $row = new Row($requirement);

        $head                          = new Head();
        $head->packageName             = 'millennial-falcon/ship';
        $head->headType                = 'branch';
        $head->headName                = 'main';
        $head->composerJsonPresent     = true;
        $head->requirements            = [
            'millennial-falcon/hyperdrive' => '^1.0',
        ];
        $head->developmentRequirements = [
            'starship/builder' => '^7.5',
            'droid/build'      => '^9.8',
        ];

        self::assertSame(
            $expected,
            $row('millennial-falcon-ship/the-ship', $head)
        );
    }
}
