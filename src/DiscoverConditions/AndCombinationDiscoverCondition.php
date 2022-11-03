<?php

namespace Spatie\LaravelAutoDiscoverer\DiscoverConditions;

use ReflectionClass;

class AndCombinationDiscoverCondition extends DiscoverCondition
{
    private array $conditions;

    public function __construct(DiscoverCondition ...$conditions)
    {
        $this->conditions = $conditions;
    }

    public function add(DiscoverCondition $condition): static
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
