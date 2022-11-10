<?php

namespace Spatie\StructureDiscoverer\DiscoverConditions;

use Closure;
use ReflectionClass;
use Spatie\StructureDiscoverer\Data\DiscoveredData;

class CustomDiscoverCondition extends DiscoverCondition
{
    public function __construct(protected Closure $closure)
    {
    }

    public function satisfies(DiscoveredData $discoveredData): bool
    {
        return ($this->closure)($discoveredData);
    }
}
