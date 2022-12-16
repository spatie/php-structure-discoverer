<?php

namespace Spatie\StructureDiscoverer\DiscoverConditions;

use Spatie\StructureDiscoverer\Data\DiscoveredClass;
use Spatie\StructureDiscoverer\Data\DiscoveredStructure;

class ExtendsDiscoverCondition extends DiscoverCondition
{
    /** @var string[] */
    private array $classes;

    public function __construct(string ...$classes)
    {
        $this->classes = $classes;
    }

    public function satisfies(DiscoveredStructure $discoveredData): bool
    {
        if ($discoveredData instanceof DiscoveredClass) {
            $extends = $discoveredData->extends === null
                ? []
                : [$discoveredData->extends];

            $foundExtends = array_filter(
                $discoveredData->extendsChain ?? $extends,
                fn (string $class) => in_array($class, $this->classes)
            );

            return count($foundExtends) > 0;
        }

        return false;
    }
}
