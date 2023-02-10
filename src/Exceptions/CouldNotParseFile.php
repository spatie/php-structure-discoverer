<?php

namespace Spatie\StructureDiscoverer\Exceptions;

use Exception;
use Throwable;

class CouldNotParseFile extends Exception
{
    public function __construct(
        string $file,
        Throwable $previous
    ) {
        parent::__construct(
            "Could not parse file {$file} because: {$previous->getMessage()}",
            previous: $previous,
        );
    }
}
