<?php

namespace Spatie\StructureDiscoverer\Exceptions;

use Exception;

class InvalidReflection extends Exception
{
    public static function create(string $expected, object $reflection): self
    {
        return new self("Tried reflecting, expected {$expected} but received: ".$reflection::class);
    }

    public static function expectedClass(): self
    {
        return new self("Tried reflecting, expected class");
    }

    public static function expectedInterface(): self
    {
        return new self("Tried reflecting, expected class");
    }

    public static function expectedEnum(): self
    {
        return new self("Tried reflecting, expected enum");
    }

    public static function expectedTrait(): self
    {
        return new self("Tried reflecting, expected trait");
    }
}
