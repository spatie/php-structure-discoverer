<?php

namespace Spatie\StructureDiscoverer\DiscoverConditions;

use ReflectionClass;
use Spatie\StructureDiscoverer\Data\DiscoveredClass;
use Spatie\StructureDiscoverer\Data\DiscoveredData;
use Spatie\StructureDiscoverer\Data\DiscoveredInterface;

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
