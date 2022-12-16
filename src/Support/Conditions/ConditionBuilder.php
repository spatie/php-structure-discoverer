<?php

namespace Spatie\StructureDiscoverer\Support\Conditions;

use Spatie\StructureDiscoverer\DiscoverConditions\ExactDiscoverCondition;
use Spatie\StructureDiscoverer\Support\Conditions\HasConditions;
use Spatie\StructureDiscoverer\Support\Conditions\HasConditionsTrait;

class ConditionBuilder implements HasConditions
{
    use HasConditionsTrait;

    public function __construct(
        public ExactDiscoverCondition $conditions = new ExactDiscoverCondition()
    ) {
    }

    public function conditionsStore(): ExactDiscoverCondition
    {
        return $this->conditions;
    }
}
