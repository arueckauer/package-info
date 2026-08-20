<?php

declare(strict_types=1);

namespace PackageInfo\Output\Table;

final readonly class RowFactory
{
    public function __invoke(): Row
    {
        return new Row();
    }
}
