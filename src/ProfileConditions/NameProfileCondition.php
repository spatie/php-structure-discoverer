<?php

namespace Spatie\LaravelAutoDiscoverer\ProfileConditions;

use ReflectionClass;

class NameProfileCondition extends ProfileCondition
{
    public function __construct(private string $name)
    {
    }

    public function satisfies(ReflectionClass $reflectionClass): bool
    {
        return $reflectionClass->getName() === $this->name;
    }
}
