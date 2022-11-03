<?php

namespace Spatie\LaravelAutoDiscoverer\DiscoverConditions;

use ReflectionClass;

class ImplementsDiscoverCondition extends DiscoverCondition
{
    public function __construct(private string $interface)
    {
    }

    public function satisfies(ReflectionClass $reflectionClass): bool
    {
        return $reflectionClass->implementsInterface($this->interface) && $reflectionClass->name !== $this->interface;
    }
}
