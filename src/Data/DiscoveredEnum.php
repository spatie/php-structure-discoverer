<?php

namespace Spatie\StructureDiscoverer\Data;

use Spatie\StructureDiscoverer\Enums\DiscoveredEnumType;
use Spatie\StructureDiscoverer\Enums\DiscoveredStructureType;

/**
 * @property array<string> $implements
 * @property array<DiscoveredAttribute> $attributes
 * @property ?array<string> $implementsChain
 */
class DiscoveredEnum extends DiscoveredStructure
{
    public function __construct(
        public string $name,
        public string $file,
        public string $namespace,
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
