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

    public function add(ProfileCondition $condition): static
    {
        $this->conditions[] = $condition;

        return $this;
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
