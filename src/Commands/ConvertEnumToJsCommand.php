<?php

declare(strict_types=1);

namespace Hackel\EnumToJs\Commands;

use Hackel\EnumToJs\Contracts\EnumFinder;
use Hackel\EnumToJs\Contracts\EnumToJson;
use Hackel\EnumToJs\Contracts\Filesystem;
use Hackel\EnumToJs\Contracts\Writer;
use Hackel\EnumToJs\Exceptions\FileAlreadyExistsException;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Composer;
use ReflectionEnum;
use UnitEnum;

final class ConvertEnumToJsCommand extends Command implements PromptsForMissingInput
{
    protected $signature = "enum-to-js:convert {enums?*}
        {--source= : The source Enum directory (default: app/Enums)}
        {--dest= : The destination JavaScript directory}
        {--no-dump-autoload : Do not try to run composer dump-autoload prior to converting}
        {--clean : Clean the destination directory before converting}";

    protected $description = "Convert PHP Enums to JavaScript objects";

    public function __construct(
        private readonly Composer $composer,
        private readonly EnumToJson $convertEnumToJs,
        private readonly EnumFinder $findEnums,
        private readonly Filesystem $filesystem,
        private readonly Writer $writer,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        /** @var string $source */
        $source = $this->option('source') ?? $this->laravel->basePath('app/Enums');

        if (!$this->filesystem->isDirectory($source)) {
            $this->error("The source path {$source} is not valid.");

            return 1;
        }

        /** @var string $dest */
        $dest = $this->option('dest') ?? $this->laravel->basePath('resources/js/enums');
        $this->filesystem->ensureDirectoryExists($dest);
        if (!$this->filesystem->isWritable($dest)) {
            $this->error("The destination path {$dest} is not writable.");

            return 1;
        }

        $this->info("Converting Enums from {$source} to JavaScript objects in {$dest}:");

        if ($this->option('no-dump-autoload') === false) {
            $this->composer->dumpOptimized();
        }

        $enums = $this->findEnums->findEnums($source);

        if ($enums->isEmpty()) {
            $this->error("No Enums found in {$source}.");

            return 1;
        }

        if ($this->option('clean')) {
            $this->filesystem->cleanDirectory($dest);
        }

        $this->withProgressBar(
            $enums,
            function (string $enum) use ($source, $dest) {
                $this->newLine();
                /** @var class-string<UnitEnum> $enum */
                $this->convertEnumToJs($enum, $source, $dest);
            },
        );

        $this->newLine()->info('Successfully converted ' . $enums->count() . ' Enums to JavaScript objects.');

        return 0;
    }

    /**
     * @param class-string<UnitEnum> $enum
     */
    private function convertEnumToJs(string $enum, string $source, string $dest): void
    {
        try {
            $jsObject = $this->convertEnumToJs->convert($enum);
            $outputPath = $this->getOutputPath($enum, $source, $dest);
            $jsPath = $this->writer->write($enum, $jsObject, $outputPath);
        } catch (FileAlreadyExistsException $e) {
            $this->warn("Skipping {$enum} - {$e->getFileName()} already exists.");

            return;
        } catch (\Throwable $e) {
            $this->error("Failed to convert {$enum}: {$e->getMessage()}");

            return;
        }
        $fileName = basename($jsPath);
        $this->info("Converted {$enum} to {$fileName}");
    }

    /**
     * @param class-string<UnitEnum> $enum
     * @throws \ReflectionException
     */
    private function getOutputPath(string $enum, string $source, string $dest): string
    {
        $reflector = new ReflectionEnum($enum);
        $enumPath = $reflector->getFileName();

        if ($enumPath === false) {
            throw new \RuntimeException("Invalid Enum: {$enum}"); // @codeCoverageIgnore
        }

        $relativePath = str_replace($source, '', $enumPath);
        $dirname = dirname($relativePath);

        return $dest . ($dirname === '/' ? '' : $dirname);
    }
}
