<?php

namespace Spatie\LaravelAutoDiscoverer\DiscoverConditions;

use Closure;
use ReflectionClass;

class CustomDiscoverCondition extends DiscoverCondition
{
    public function __construct(protected Closure $closure)
    {
    }

    public function satisfies(ReflectionClass $reflectionClass): bool
    {
        return ($this->closure)($reflectionClass);
    }
}
