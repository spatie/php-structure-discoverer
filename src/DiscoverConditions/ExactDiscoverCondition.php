<?php

namespace Spatie\StructureDiscoverer\DiscoverConditions;

use Spatie\StructureDiscoverer\Data\DiscoveredData;
use Spatie\StructureDiscoverer\DiscoverConditionFactory;

class ExactDiscoverCondition extends DiscoverCondition
{
    /** @var DiscoverCondition[] */
    private array $conditions = [];

    public function __construct(DiscoverCondition|DiscoverConditionFactory ...$conditions)
    {
        foreach ($conditions as $condition) {
            $this->add($condition);
        }
    }

    public function add(DiscoverCondition|DiscoverConditionFactory $condition): static
    {
        $this->conditions[] = $condition instanceof DiscoverConditionFactory
            ? $condition->conditions
            : $condition;

        return $this;
    }

    public function satisfies(DiscoveredData $discoveredData): bool
    {
        foreach ($this->conditions as $condition) {
            if (! $condition->satisfies($discoveredData)) {
                return false;
            }
        }

        return true;
    }
}
