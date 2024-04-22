<?php

declare(strict_types=1);

namespace Hackel\EnumToJs\Contracts;

interface EnumToJson
{
    public function convert(string $enum): string;
}
