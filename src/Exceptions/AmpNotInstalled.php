<?php

namespace Spatie\StructureDiscoverer\Exceptions;

use Exception;

class AmpNotInstalled extends Exception
{
    public static function create(): self
    {
        return new self('Parallel structure discovery requires amphp/parallel to be installed.');
    }
}
