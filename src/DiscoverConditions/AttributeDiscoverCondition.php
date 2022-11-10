<?php

namespace Spatie\StructureDiscoverer\DiscoverConditions;

use Spatie\StructureDiscoverer\Data\DiscoveredAttribute;
use Spatie\StructureDiscoverer\Data\DiscoveredClass;
use Spatie\StructureDiscoverer\Data\DiscoveredData;
use Spatie\StructureDiscoverer\Data\DiscoveredEnum;
use Spatie\StructureDiscoverer\Data\DiscoveredInterface;

class AttributeDiscoverCondition extends DiscoverCondition
{
    /** @var string[] */
    private array $classes;

    public function __construct(
        string ...$classes,
    ) {
        $this->classes = $classes;
    }

    public function satisfies(DiscoveredData $discoveredData): bool
    {
        $hasAttributes = $discoveredData instanceof DiscoveredInterface
            || $discoveredData instanceof DiscoveredEnum
            || $discoveredData instanceof DiscoveredClass;

        if (! $hasAttributes) {
            return false;
        }

        $foundAttributes = array_filter(
            $discoveredData->attributes,
            fn (DiscoveredAttribute $attribute) => in_array($attribute->class, $this->classes)
        );

        return count($foundAttributes) > 0;
    }
}
