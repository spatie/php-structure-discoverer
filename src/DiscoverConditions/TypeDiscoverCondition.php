<?php

namespace Spatie\StructureDiscoverer\DiscoverConditions;

use Spatie\StructureDiscoverer\Data\DiscoveredStructure;
use Spatie\StructureDiscoverer\Enums\DiscoveredStructureType;

class TypeDiscoverCondition extends DiscoverCondition
{
    /** @var DiscoveredStructureType[] */
    private array $types;

    public function __construct(DiscoveredStructureType ...$types)
    {
        $this->types = $types;
    }

    public function satisfies(DiscoveredStructure $discoveredData): bool
    {
        return in_array($discoveredData->getType(), $this->types);
    }
}
