<?php

namespace Spatie\LaravelAutoDiscoverer\ProfileReferences;

use Closure;
use ReflectionClass;

class CustomProfileReference extends ProfileReference
{
    public function __construct(protected Closure $closure)
    {
    }

    public function satisfies(ReflectionClass $reflectionClass): bool
    {
        return ($this->closure)($reflectionClass);
    }
}
