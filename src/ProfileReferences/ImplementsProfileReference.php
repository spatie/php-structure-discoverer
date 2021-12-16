<?php

namespace Spatie\LaravelAutoDiscoverer\ProfileReferences;

use ReflectionClass;

class ImplementsProfileReference extends ProfileReference
{
    public function __construct(private string $interface)
    {
    }

    public function satisfies(ReflectionClass $reflectionClass): bool
    {
        return $reflectionClass->implementsInterface($this->interface) && $reflectionClass->name !== $this->interface;
    }
}
