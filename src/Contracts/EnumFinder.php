<?php

declare(strict_types=1);

namespace Hackel\EnumToJs\Contracts;

use Illuminate\Support\Collection;
use UnitEnum;

interface EnumFinder
{
    /**
     * Retrieve a collection of all the Enums in the given path.
     *
     * @return Collection<int, class-string<UnitEnum>>
     */
    public function findEnums(string $path): Collection;
}
