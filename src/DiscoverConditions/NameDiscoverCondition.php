<?php

namespace Spatie\StructureDiscoverer\DiscoverConditions;

use ReflectionClass;
use Spatie\StructureDiscoverer\Data\DiscoveredData;

class NameDiscoverCondition extends DiscoverCondition
{
    /** @var string[] */
    private array $names;

    public function __construct(string ...$names)
    {
        $this->names = $names;
    }

    public function satisfies(DiscoveredData $discoveredData): bool
    {
        return in_array($discoveredData->name, $this->names);
    }
}
