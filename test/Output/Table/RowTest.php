<?php

declare(strict_types=1);

namespace PackageInfoTest\Output\Table;

use PackageInfo\Output\Table\Row;
use PackageInfo\Repository\Head;
use PackageInfo\Requirement\Check\Result;
use PackageInfo\Requirement\Checker;
use PackageInfo\Requirement\Renderer;
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
            'Requirements'            => 'millennial-falcon/hyperdrive: <info>^1.1</info>',
            'Dev Requirements'        => "starship/builder: <info>~7.6.3</info>\ndroid/build: <info>9.9.9</info>",
        ];

        $checker = $this->createMock(Checker::class);

        $resultA                    = new Result();
        $resultA->requirementName   = 'millennial-falcon/hyperdrive';
        $resultA->hasRequirement    = true;
        $resultA->versionConstraint = '^1.1';
        $resultA->isSupported       = true;

        $checker
            ->expects(self::once())
            ->method('checkRequirements')
            ->willReturn([$resultA]);

        $resultB                    = new Result();
        $resultB->requirementName   = 'starship/builder';
        $resultB->hasRequirement    = true;
        $resultB->versionConstraint = '~7.6.3';
        $resultB->isSupported       = true;

        $resultC                    = new Result();
        $resultC->requirementName   = 'droid/build';
        $resultC->hasRequirement    = true;
        $resultC->versionConstraint = '9.9.9';
        $resultC->isSupported       = true;

        $checker
            ->expects(self::once())
            ->method('checkDevelopmentRequirements')
            ->willReturn([$resultB, $resultC]);

        $row = new Row($checker, new Renderer());

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
