<?php

declare(strict_types=1);

namespace Hackel\EnumToJs;

use Hackel\EnumToJs\Contracts\Filesystem;
use Hackel\EnumToJs\Contracts\Writer as WriterContract;
use Hackel\EnumToJs\Exceptions\FileAlreadyExistsException;
use Illuminate\Contracts\Foundation\Application;

class Writer implements WriterContract
{
    public function __construct(private readonly Application $app, private readonly Filesystem $filesystem) {}

    public function write(string $enum, string $jsObject, string $outputPath): string
    {
        $outputFile = $this->getOutputFile($enum, $outputPath);

        if ($this->filesystem->exists($outputFile)) {
            throw new FileAlreadyExistsException($outputFile);
        }

        $content = $this->getStub();
        $content = str_replace('{{ $cases }}', $jsObject, $content);
        $this->filesystem->ensureDirectoryExists($outputPath);
        $this->filesystem->put($outputFile, $content);

        return $outputFile;
    }

    private function getOutputFile(string $enum, string $outputPath): string
    {
        return $outputPath . '/' . class_basename($enum) . '.js';
    }

    private function getStub(): string
    {
        return $this->filesystem->get($this->resolveStubPath('stubs/enum.stub'));
    }

    private function resolveStubPath(string $stub): string
    {
        return file_exists($customPath = $this->app->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__ . "/../$stub";
    }

}
