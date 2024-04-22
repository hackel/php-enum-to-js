<?php

declare(strict_types=1);

namespace Hackel\EnumToJs;

use Hackel\EnumToJs\Commands\ConvertEnumToJsCommand;
use Hackel\EnumToJs\Contracts\EnumFinder as EnumFinderContract;
use Hackel\EnumToJs\Contracts\EnumToJson as EnumToJsContract;
use Hackel\EnumToJs\Contracts\Filesystem as FilesystemContract;
use Hackel\EnumToJs\Contracts\Writer as WriterContract;
use Illuminate\Support\ServiceProvider;

final class EnumToJsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands(
                commands: [
                    ConvertEnumToJsCommand::class,
                ],
            );
        }
    }

    public function register()
    {
        $this->app->bind(EnumToJsContract::class, EnumToJson::class);
        $this->app->bind(FilesystemContract::class, Filesystem::class);
        $this->app->bind(EnumFinderContract::class, EnumFinder::class);
        $this->app->bind(WriterContract::class, Writer::class);
    }
}
