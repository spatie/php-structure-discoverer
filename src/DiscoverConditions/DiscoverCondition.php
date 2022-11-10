<?php

namespace Spatie\StructureDiscoverer\DiscoverConditions;

use Closure;
use ReflectionClass;
use Spatie\StructureDiscoverer\Data\DiscoveredData;
use Spatie\StructureDiscoverer\DiscoverConditionFactory;

abstract class DiscoverCondition
{
    abstract public function satisfies(DiscoveredData $discoveredData): bool;

    public static function create(): DiscoverConditionFactory
    {
        return new DiscoverConditionFactory();
    }
}
