<?php

namespace Spatie\StructureDiscoverer\Exceptions;

use Exception;

class CallbackRequired extends Exception
{
    public static function create(string $identifier)
    {
        return new self("Tried getting the classes for structure discover profile `{$identifier}` which was not executed yet due to Laravel not completely booted up (yet). Please add a callback to retrieve the classes later on instead of directly retrieving the classes.");
    }
}
