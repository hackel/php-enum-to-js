<?php

declare(strict_types=1);

namespace Hackel\EnumToJs\Contracts;

/**
 * @see \Illuminate\Filesystem\Filesystem
 */
interface Filesystem
{
    /** @phpstan-ignore-next-line */
    public function cleanDirectory($directory);
    /** @phpstan-ignore-next-line */
    public function ensureDirectoryExists($path, $mode = 0755, $recursive = true);

    /** @phpstan-ignore-next-line */
    public function exists($path);

    /** @phpstan-ignore-next-line */
    public function get($path, $lock = false);

    /** @phpstan-ignore-next-line */
    public function isDirectory($directory);

    /** @phpstan-ignore-next-line */
    public function isWritable($path);

    /** @phpstan-ignore-next-line */
    public function put($path, $contents, $lock = false);
}
