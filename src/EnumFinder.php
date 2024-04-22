<?php

declare(strict_types=1);

namespace Hackel\EnumToJs;

use Hackel\EnumToJs\Contracts\EnumFinder as EnumFinderContract;
use Hackel\EnumToJs\Exceptions\NamespaceNotFoundException;
use Illuminate\Support\Collection;
use ReflectionClass;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use UnitEnum;

final class EnumFinder implements EnumFinderContract
{
    public function __construct(private readonly Finder $finder) {}

    /**
     * Retrieve a collection of all the Enums in the given path.
     *
     * @return Collection<int, class-string<UnitEnum>>
     */
    public function findEnums(string $path): Collection
    {
        $files = $this->getFiles($path);

        /** @var Collection<int, class-string<UnitEnum>> $enums */
        $enums = $files
            ->map(function (SplFileInfo $file): ?string {
                try {
                    /** @var class-string $class */
                    $class = $this->getNamespace($file) . '\\' . $file->getBasename('.php');

                    return $class;
                } catch (NamespaceNotFoundException) {
                    return null;
                }
            })
            ->filter($this->enumIsValid(...))
            ->values();

        return $enums;
    }

    /**
     * Retrieve a collection of all PHP files in the given path.
     *
     * @return Collection<string, SplFileInfo>
     */
    private function getFiles(string $path): Collection
    {
        $results = $this->finder->files()->in($path)->name('*.php');

        return new Collection($results);
    }

    /**
     * Extract the namespace from the given file.
     */
    private function getNamespace(SplFileInfo $file): string
    {
        $matches = [];
        if (preg_match('/\nnamespace (.*);/', $file->getContents(), $matches) !== 1) {
            throw new NamespaceNotFoundException('Namespace not found in ' . $file->getRealPath());
        }
        /** @var string $namespace */
        $namespace = $matches[1];

        return $namespace;
    }

    /**
     * Test whether the given class-string is a valid Enum.
     *
     * @param ?class-string  $enum
     */
    private function enumIsValid(?string $enum): bool
    {
        if (!$enum || !\class_exists($enum)) {
            return false;
        }

        $reflector = new ReflectionClass($enum);

        return $reflector->isEnum();
    }
}
