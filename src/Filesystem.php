<?php

declare(strict_types=1);

namespace Hackel\EnumToJs;

use Illuminate\Filesystem\Filesystem as IlluminateFilesystem;

class Filesystem extends IlluminateFilesystem implements Contracts\Filesystem {}
