<?php

namespace Spatie\StructureDiscoverer\Support;

class ProfileCondition implements HasConditions
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
{

}
