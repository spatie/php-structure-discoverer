<?php

namespace Spatie\LaravelAutoDiscoverer\ProfileConditions;

use Closure;
use ReflectionClass;

class CustomProfileCondition extends ProfileCondition
{
    public function __construct(protected Closure $closure)
    {
    }

    public function satisfies(ReflectionClass $reflectionClass): bool
    {
        return ($this->closure)($reflectionClass);
    }
}
