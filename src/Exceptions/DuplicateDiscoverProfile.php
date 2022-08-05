<?php

namespace Spatie\LaravelAutoDiscoverer\Exceptions;

use Exception;

class DuplicateDiscoverProfile extends Exception
{
    public static function forIdentifier(string $identifier): self
    {
        return new self("Could not add discover profile `{$identifier}`, because a profile with such identifier already exists.");
    }
}
