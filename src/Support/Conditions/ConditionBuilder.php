<?php

namespace Spatie\StructureDiscoverer\Support\Conditions;

use Spatie\StructureDiscoverer\DiscoverConditions\ExactDiscoverCondition;

class ConditionBuilder implements HasConditions
{
    use HasConditionsTrait;

    public function __construct(
        public ExactDiscoverCondition $conditions = new ExactDiscoverCondition()
    ) {
    }

    public static function create(): self
    {
        return new self();
    }

    public function conditionsStore(): ExactDiscoverCondition
    {
        return $this->conditions;
    }
}
