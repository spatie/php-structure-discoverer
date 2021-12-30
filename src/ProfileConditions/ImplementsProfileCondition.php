<?php

namespace Spatie\LaravelAutoDiscoverer\ProfileConditions;

use ReflectionClass;

class ImplementsProfileCondition extends ProfileCondition
{
    public function __construct(private string $interface)
    {
    }

    public function satisfies(ReflectionClass $reflectionClass): bool
    {
        return $reflectionClass->implementsInterface($this->interface) && $reflectionClass->name !== $this->interface;
    }
}
