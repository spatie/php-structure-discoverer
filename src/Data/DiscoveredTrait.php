<?php

namespace Spatie\StructureDiscoverer\Data;

use Spatie\StructureDiscoverer\Enums\DiscoveredStructureType;

class DiscoveredTrait extends DiscoveredStructure
{
    public function getType(): DiscoveredStructureType
    {
        return DiscoveredStructureType::Trait;
    }
}
