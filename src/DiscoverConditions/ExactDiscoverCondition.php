<?php

namespace Spatie\StructureDiscoverer\DiscoverConditions;

use Spatie\StructureDiscoverer\Data\DiscoveredStructure;
use Spatie\StructureDiscoverer\Support\Conditions\HasConditions;

class ExactDiscoverCondition extends DiscoverCondition
{
    /** @var DiscoverCondition[] */
    private array $conditions = [];

    public function __construct(DiscoverCondition|HasConditions ...$conditions)
    {
        foreach ($conditions as $condition) {
            $this->add($condition);
        }
    }

    public function add(DiscoverCondition|HasConditions $condition): static
    {
        $this->conditions[] = $condition instanceof HasConditions
            ? $condition->conditionsStore()
            : $condition;

        return $this;
    }

    public function satisfies(DiscoveredStructure $discoveredData): bool
    {
        foreach ($this->conditions as $condition) {
            if (! $condition->satisfies($discoveredData)) {
                return false;
            }
        }

        return true;
    }
}
