<?php

namespace Spatie\StructureDiscoverer\Exceptions;

use Exception;

class InvalidDiscoverCacheId extends Exception
{
    public static function create(?string $id): self
    {
        return new self("Discover cache id {$id} is invalid, it can only contain letters, numbers and dashes");
    }
}
