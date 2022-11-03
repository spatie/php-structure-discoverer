<?php

namespace Spatie\LaravelAutoDiscoverer\DiscoverConditions;

use ReflectionClass;

class NameDiscoverCondition extends DiscoverCondition
{
    public function __construct(private string $name)
    {
    }

    public function satisfies(ReflectionClass $reflectionClass): bool
    {
        return $reflectionClass->getName() === $this->name;
    }
}
