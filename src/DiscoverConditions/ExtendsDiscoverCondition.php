<?php

namespace Spatie\LaravelAutoDiscoverer\DiscoverConditions;

use ReflectionClass;

class ExtendsDiscoverCondition extends DiscoverCondition
{
    public function __construct(private string $class)
    {
    }

    public function satisfies(ReflectionClass $reflectionClass): bool
    {
        return $reflectionClass->isSubclassOf($this->class);
    }
}
