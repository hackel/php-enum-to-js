<?php

declare(strict_types=1);

use Hackel\EnumToJs\Contracts\Filesystem;
use Hackel\EnumToJs\Exceptions\FileAlreadyExistsException;
use Hackel\EnumToJs\Tests\Enums\Color;
use Hackel\EnumToJs\Writer;
use Illuminate\Contracts\Foundation\Application;

describe(Writer::class, function () {
    it('should write the enum to the given path', function () {
        $app = $this->mock(Application::class)
            ->shouldReceive('basePath')
            ->with('stubs/enum.stub')
            ->andReturn('stubs/enum.stub')
            ->getMock();

        $filesystem = $this->mock(Filesystem::class)
            ->shouldReceive('get')
            ->with('stubs/enum.stub')
            ->andReturn('stub content {{ $cases }}')
            ->shouldReceive('exists')
            ->with('output/path/Color.js')
            ->andReturn(false)
            ->shouldReceive('ensureDirectoryExists')
            ->shouldReceive('put')
            ->with('output/path/Color.js', 'stub content js object')
            ->andReturn(1024)
            ->getMock();

        $writer = new Writer($app, $filesystem);

        $outputFile = $writer->write(Color::class, 'js object', 'output/path');

        expect($outputFile)->toBe('output/path/Color.js');
    });

    it('should throw an exception if the destination file already exists', function () {
        $app = $this->mock(Application::class)
            ->shouldReceive('basePath')
            ->with('stubs/enum.stub')
            ->andReturn('stubs/enum.stub')
            ->getMock();

        $filesystem = $this->mock(Filesystem::class)
            ->shouldReceive('get')
            ->with('stubs/enum.stub')
            ->andReturn('stub content {{ $cases }}')
            ->shouldReceive('exists')
            ->with('output/path/Color.js')
            ->andReturn(true)
            ->getMock();

        $writer = new Writer($app, $filesystem);

        $writer->write(Color::class, 'js object', 'output/path');
    })->throws(FileAlreadyExistsException::class, 'The file `output/path/Color.js` already exists.');
});
