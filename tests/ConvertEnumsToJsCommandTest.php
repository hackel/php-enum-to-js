<?php

declare(strict_types=1);

use Hackel\EnumToJs\Commands\ConvertEnumToJsCommand;
use Hackel\EnumToJs\Contracts\EnumFinder;
use Hackel\EnumToJs\Contracts\EnumToJson;
use Hackel\EnumToJs\Contracts\Filesystem;
use Hackel\EnumToJs\Contracts\Writer;
use Hackel\EnumToJs\Exceptions\FileAlreadyExistsException;
use Hackel\EnumToJs\Tests\Enums\Color;
use Hackel\EnumToJs\Tests\Enums\Nested\Path\DayOfWeek;
use Hackel\EnumToJs\Tests\Enums\Number;
use Illuminate\Support\Composer;

describe('ConvertEnumsToJsCommand', function () {
    it('can run the command successfully', function () {
        $this->mock(Composer::class)->shouldReceive('dumpOptimized');
        $this->mock(Filesystem::class)
            ->shouldReceive('isDirectory')
            ->andReturn(true)
            ->shouldReceive('ensureDirectoryExists')
            ->shouldReceive('isWritable')
            ->andReturn(true);
        $this->mock(EnumFinder::class)
            ->shouldReceive('findEnums')
            ->andReturn(collect([
                Color::class,
                Number::class,
                DayOfWeek::class,
            ]));
        $this->mock(EnumToJson::class)
            ->shouldReceive('convert')
            ->andReturnValues([
                <<<'JSON'
                {
                    "RED": "red",
                    "GREEN": "green",
                    "BLUE": "blue"
                }
                JSON,
                <<<'JSON'
                {
                    "ONE": 1,
                    "TWO": 2,
                    "THREE": 3
                }
                JSON,
                <<<'JSON'
                {
                    "SUNDAY": "Sunday",
                    "MONDAY": "Monday",
                    "TUESDAY": "Tuesday",
                    "WEDNESDAY": "Wednesday",
                    "THURSDAY": "Thursday",
                    "FRIDAY": "Friday",
                    "SATURDAY": "Saturday"
                }
                JSON,
            ]);
        $this->mock(Writer::class)
            ->shouldReceive('write')
            ->times(3)
            ->andReturnValues([
                'resources/js/enums/Color.js',
                'resources/js/enums/Number.js',
                'resources/js/enums/Nested/Path/DayOfWeek.js',
            ]);

        $source = __DIR__ . '/Enums';
        $dest = __DIR__ . '/out';

        $response = $this
            ->artisan(ConvertEnumToJsCommand::class, [
                '--source' => $source,
                '--dest' => $dest,
            ]);

        $response
            ->expectsOutput("Converting Enums from {$source} to JavaScript objects in {$dest}:")
            ->expectsOutput('Converted Hackel\EnumToJs\Tests\Enums\Color to Color.js')
            ->expectsOutput('Converted Hackel\EnumToJs\Tests\Enums\Number to Number.js')
            ->expectsOutput('Converted Hackel\EnumToJs\Tests\Enums\Nested\Path\DayOfWeek to DayOfWeek.js')
            ->expectsOutput('Successfully converted 3 Enums to JavaScript objects.')
            ->assertSuccessful();
    });

    it('returns an error when the source is not a directory', function () {
        $this->mock(Filesystem::class)
            ->shouldReceive('isDirectory')
            ->andReturn(false);

        $source = __DIR__;

        $response = $this->artisan(ConvertEnumToJsCommand::class, ['--source' => $source]);

        $response
            ->assertFailed()
            ->expectsOutput("The source path {$source} is not valid.");
    });

    it('returns an error when the source directory does not exist', function () {
        $this->mock(Filesystem::class)
            ->shouldReceive('isDirectory')
            ->andReturn(true)
            ->shouldReceive('ensureDirectoryExists')
            ->shouldReceive('isWritable')
            ->andReturn(false);

        $source = __DIR__;
        $dest = __DIR__;

        $response = $this->artisan(ConvertEnumToJsCommand::class, [
            '--source' => $source,
            '--dest' => $dest,
        ]);

        $response
            ->assertFailed()
            ->expectsOutput("The destination path {$dest} is not writable.");
    });

    it('should return an error when no enums are found', function () {
        $this->mock(Composer::class)->shouldReceive('dumpOptimized');
        $this->mock(Filesystem::class)
            ->shouldReceive('isDirectory')
            ->andReturn(true)
            ->shouldReceive('ensureDirectoryExists')
            ->shouldReceive('isWritable')
            ->andReturn(true);
        $this->mock(EnumFinder::class)
            ->shouldReceive('findEnums')
            ->andReturn(collect());

        $this
            ->artisan(ConvertEnumToJsCommand::class)
            ->assertFailed()
            ->expectsOutput("No Enums found in {$this->app->basePath('app/Enums')}.");
    });

    it('should clean the destination directory when the clean option is given', function () {
        $this->mock(Composer::class)->shouldReceive('dumpOptimized');
        $this->mock(Filesystem::class)
            ->shouldReceive('isDirectory')
            ->andReturn(true)
            ->shouldReceive('ensureDirectoryExists')
            ->shouldReceive('isWritable')
            ->andReturn(true)
            ->shouldReceive('cleanDirectory')
            ->andReturn(true);
        $this->mock(EnumFinder::class)
            ->shouldReceive('findEnums')
            ->andReturn(collect([Color::class]));
        $this->mock(EnumToJson::class)
            ->shouldReceive('convert')
            ->andReturn('test');
        $this->mock(Writer::class)
            ->shouldReceive('write')
            ->andReturn('resources/js/enums/Color.js');

        $this
            ->artisan(ConvertEnumToJsCommand::class, ['--clean' => true])
            ->assertSuccessful();
    });

    it('should return an error when a destination file already exists', function () {
        $this->mock(Composer::class)->shouldReceive('dumpOptimized');
        $this->mock(Filesystem::class)
            ->shouldReceive('isDirectory')
            ->andReturn(true)
            ->shouldReceive('ensureDirectoryExists')
            ->shouldReceive('isWritable')
            ->andReturn(true);
        $this->mock(EnumFinder::class)
            ->shouldReceive('findEnums')
            ->andReturn(collect([Color::class]));
        $this->mock(EnumToJson::class)
            ->shouldReceive('convert')
            ->andReturn('test');
        $this->mock(Writer::class)
            ->shouldReceive('write')
            ->andThrow(new FileAlreadyExistsException('Color.js'));

        $this
            ->artisan(ConvertEnumToJsCommand::class)
            ->assertSuccessful()
            ->expectsOutput("Skipping Hackel\EnumToJs\Tests\Enums\Color - Color.js already exists.");
    });

    it('should return an error when an unexpected exception is thrown', function () {
        $this->mock(Composer::class)->shouldReceive('dumpOptimized');
        $this->mock(Filesystem::class)
            ->shouldReceive('isDirectory')
            ->andReturn(true)
            ->shouldReceive('ensureDirectoryExists')
            ->shouldReceive('isWritable')
            ->andReturn(true);
        $this->mock(EnumFinder::class)
            ->shouldReceive('findEnums')
            ->andReturn(collect([Color::class]));
        $this->mock(EnumToJson::class)
            ->shouldReceive('convert')
            ->andReturn('test');
        $this->mock(Writer::class)
            ->shouldReceive('write')
            ->andThrow(Exception::class, 'test error');

        $this
            ->artisan(ConvertEnumToJsCommand::class)
            ->assertSuccessful()
            ->expectsOutput(
                "Failed to convert Hackel\EnumToJs\Tests\Enums\Color: test error"
            );
    });

    it('should return an error when an internal enum is specified', function () {
        $this->mock(Composer::class)->shouldReceive('dumpOptimized');
        $this->mock(Filesystem::class)
            ->shouldReceive('isDirectory')
            ->andReturn(true)
            ->shouldReceive('ensureDirectoryExists')
            ->shouldReceive('isWritable')
            ->andReturn(true);
        $this->mock(EnumFinder::class)
            ->shouldReceive('findEnums')
            ->andReturn(collect([Color::class]));
        $this->mock(EnumToJson::class)
            ->shouldReceive('convert')
            ->andReturn('test');
        $this->mock(Writer::class)
            ->shouldReceive('write')
            ->andThrow(Exception::class, 'test error');

        $this
            ->artisan(ConvertEnumToJsCommand::class)
            ->assertSuccessful()
            ->expectsOutput(
                "Failed to convert Hackel\EnumToJs\Tests\Enums\Color: test error"
            );
    });
});
