<?php

namespace Spatie\StructureDiscoverer\DiscoverConditions;

use Spatie\StructureDiscoverer\Support\Conditions\ConditionBuilder;
use Spatie\StructureDiscoverer\Data\DiscoveredStructure;
use Spatie\StructureDiscoverer\Support\Conditions\HasConditionsTrait;

abstract class DiscoverCondition
{
    abstract public function satisfies(DiscoveredStructure $discoveredData): bool;

    public static function create(): ConditionBuilder
    {
        return new ConditionBuilder();
    }
}
