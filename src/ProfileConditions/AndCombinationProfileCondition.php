<?php

namespace Spatie\LaravelAutoDiscoverer\ProfileConditions;

use ReflectionClass;

class AndCombinationProfileCondition extends ProfileCondition
{
    private array $conditions;

    public function __construct(ProfileCondition ...$conditions)
    {
        $this->conditions = $conditions;
    }

    public function add(ProfileCondition $condition)
    {
        return $this->conditions[] = $condition;
    }

    public function satisfies(ReflectionClass $reflectionClass): bool
    {
        foreach ($this->conditions as $condition) {
            if (! $condition->satisfies($reflectionClass)) {
                return false;
            }
        }

        return true;
    }
}
