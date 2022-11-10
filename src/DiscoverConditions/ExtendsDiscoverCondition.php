<?php

namespace Spatie\StructureDiscoverer\DiscoverConditions;

use Spatie\StructureDiscoverer\Data\DiscoveredClass;
use Spatie\StructureDiscoverer\Data\DiscoveredData;

class ExtendsDiscoverCondition extends DiscoverCondition
{
    /** @var string[] */
    private array $classes;

    public function __construct(string ...$classes)
    {
        $this->classes = $classes;
    }

    public function satisfies(DiscoveredData $discoveredData): bool
    {
        if ($discoveredData instanceof DiscoveredClass) {
            return in_array($discoveredData->extends, $this->classes);
        }

        return false;
    }
}
