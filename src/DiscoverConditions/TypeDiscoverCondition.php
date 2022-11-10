<?php

namespace Spatie\StructureDiscoverer\DiscoverConditions;

use Spatie\StructureDiscoverer\Data\DiscoveredData;
use Spatie\StructureDiscoverer\Enums\DiscoveredStructureType;

class TypeDiscoverCondition extends DiscoverCondition
{
    /** @var DiscoveredStructureType[] */
    private array $types;

    public function __construct(DiscoveredStructureType ...$types)
    {
        $this->types = $types;
    }

    public function satisfies(DiscoveredData $discoveredData): bool
    {
        return in_array($discoveredData->getType(), $this->types);
    }
}
