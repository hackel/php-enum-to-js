<?php

declare(strict_types=1);

namespace Hackel\EnumToJs\Tests\Enums;

enum Color: string
{
    case RED = 'red';
    case GREEN = 'green';
    case BLUE = 'blue';
}
