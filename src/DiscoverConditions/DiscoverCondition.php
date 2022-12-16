<?php

namespace Spatie\StructureDiscoverer\DiscoverConditions;

use Spatie\StructureDiscoverer\Data\DiscoveredStructure;
use Spatie\StructureDiscoverer\DiscoverConditionFactory;

abstract class DiscoverCondition
{
    abstract public function satisfies(DiscoveredStructure $discoveredData): bool;

    public static function create(): DiscoverConditionFactory
    {
        return new DiscoverConditionFactory();
    }
}
