<?php

namespace Spatie\LaravelAutoDiscoverer\ProfileReferences;

use ReflectionClass;

class ExtendsProfileReference extends ProfileReference
{
    public function __construct(private string $class)
    {
    }

    public function satisfies(ReflectionClass $reflectionClass): bool
    {
        return $reflectionClass->isSubclassOf($this->class);
    }
}
