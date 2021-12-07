<?php

namespace Spatie\LaravelAutoDiscoverer\ProfileReferences;

use ReflectionClass;

class NameProfileReference extends ProfileReference
{
    public function __construct(private string $name)
    {
    }

    public function satisfies(ReflectionClass $reflectionClass): bool
    {
        return $reflectionClass->getName() === $this->name;
    }
}
