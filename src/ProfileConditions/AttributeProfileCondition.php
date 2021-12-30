<?php

namespace Spatie\LaravelAutoDiscoverer\ProfileConditions;

use Closure;
use ReflectionClass;

class AttributeProfileCondition extends ProfileCondition
{
    public function __construct(
        private string $class,
        private null|array|Closure $arguments = null
    ) {
    }

    public function satisfies(ReflectionClass $reflectionClass): bool
    {
        foreach ($reflectionClass->getAttributes($this->class) as $reflectionAttribute) {
            if ($this->arguments === null) {
                return true;
            }

            if (is_callable($this->arguments) && ($this->arguments)($reflectionAttribute->newInstance())) {
                return true;
            }

            if ($this->arguments === $reflectionAttribute->getArguments()) {
                return true;
            }
        }

        return false;
    }
}
