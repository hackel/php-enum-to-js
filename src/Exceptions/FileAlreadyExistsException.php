<?php

declare(strict_types=1);

namespace Hackel\EnumToJs\Exceptions;

use RuntimeException;

class FileAlreadyExistsException extends RuntimeException
{
    public function __construct(private readonly string $fileName)
    {
        parent::__construct("The file `{$fileName}` already exists.");
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }
}
