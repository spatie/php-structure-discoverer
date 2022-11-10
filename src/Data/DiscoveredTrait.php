<?php

namespace Spatie\StructureDiscoverer\Data;

use Illuminate\Support\Collection;
use Spatie\StructureDiscoverer\Collections\UsageCollection;
use Spatie\StructureDiscoverer\Enums\DiscoveredStructureType;

class DiscoveredTrait extends DiscoveredData
{
    public function getType(): DiscoveredStructureType
    {
        return DiscoveredStructureType::Trait;
    }
}
