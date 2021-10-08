<?php

declare(strict_types=1);

namespace PackageInfoTest\Requirement;

use PackageInfo\Requirement\Check\Result;
use PackageInfo\Requirement\Renderer;
use PHPUnit\Framework\TestCase;

class RendererTest extends TestCase
{
    /**
     * @covers \PackageInfo\Requirement\Renderer::__invoke
     */
    public function test__invoke_success(): void
    {
        $result                    = new Result();
        $result->requirementName   = 'millennial-falcon/hyperdrive';
        $result->versionConstraint = '^1.0';
        $result->hasRequirement    = true;
        $result->isSupported       = true;

        self::assertSame(
            'millennial-falcon/hyperdrive: <info>^1.0</info>',
            (new Renderer())($result)
        );
    }

    /**
     * @covers \PackageInfo\Requirement\Renderer::__invoke
     */
    public function test__invoke_failure(): void
    {
        $result                    = new Result();
        $result->requirementName   = 'millennial-falcon/hyperdrive';
        $result->versionConstraint = '^1.0';
        $result->hasRequirement    = true;
        $result->isSupported       = false;

        self::assertSame(
            'millennial-falcon/hyperdrive: <comment>^1.0</comment>',
            (new Renderer())($result)
        );
    }

    /**
     * @covers \PackageInfo\Requirement\Renderer::__invoke
     */
    public function test__invoke_requirement_not_present(): void
    {
        $result                  = new Result();
        $result->requirementName = 'millennial-falcon/hyperdrive';
        $result->hasRequirement  = false;

        self::assertSame(
            'millennial-falcon/hyperdrive: <error>n/a</error>',
            (new Renderer())($result)
        );
    }
}
