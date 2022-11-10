<?php

namespace Spatie\StructureDiscoverer\Data;

use Illuminate\Support\Collection;
use Spatie\StructureDiscoverer\Collections\AttributeCollection;
use Spatie\StructureDiscoverer\Collections\UsageCollection;
use Spatie\StructureDiscoverer\Enums\DiscoveredEnumType;
use Spatie\StructureDiscoverer\Enums\DiscoveredStructureType;

/**
 * @property array<string> $implements
 * @property array<DiscoveredAttribute> $attributes
 * @property ?array<string> $implementsChain
 */
class DiscoveredEnum extends DiscoveredData
{
    public function __construct(
        public string $name,
        public string $namespace,
        public string $file,
        public DiscoveredEnumType $type,
        public array $implements,
        public array $attributes,
        public ?array $implementsChain = null,
    ) {
        parent::__construct($name, $namespace, $file);
    }

    public function getType(): DiscoveredStructureType
    {
        return DiscoveredStructureType::Enum;
    }
}
