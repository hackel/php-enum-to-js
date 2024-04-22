<?php

declare(strict_types=1);

namespace Hackel\EnumToJs\Contracts;

use UnitEnum;

interface Writer
{
    /**
     * @param class-string<UnitEnum> $enum
     */
    public function write(string $enum, string $jsObject, string $outputPath): string;
}
