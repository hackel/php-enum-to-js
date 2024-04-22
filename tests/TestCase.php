<?php

declare(strict_types=1);

namespace Hackel\EnumToJs\Tests;

use Hackel\EnumToJs\EnumToJsServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            EnumToJsServiceProvider::class,
        ];
    }
}
