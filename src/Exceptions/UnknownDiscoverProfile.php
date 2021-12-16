<?php

namespace Spatie\LaravelAutoDiscoverer\Exceptions;

use Exception;

class UnknownDiscoverProfile extends Exception
{
    public static function forIdentifier(string $identifier)
    {
        return new self("Could not find discover profile `{$identifier}`, maybe it was not yet added?");
    }
}
