<?php

namespace Spatie\StructureDiscoverer\DiscoverConditions;

use Spatie\StructureDiscoverer\Data\DiscoveredStructure;

class NameDiscoverCondition extends DiscoverCondition
{
    /** @var string[] */
    private array $names;

    public function __construct(string ...$names)
    {
        $this->names = $names;
    }

    public function satisfies(DiscoveredStructure $discoveredData): bool
    {
        return in_array($discoveredData->name, $this->names);
    }
}
