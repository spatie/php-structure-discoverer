<?php

namespace Spatie\StructureDiscoverer\Data;

use Illuminate\Support\Collection;
use Spatie\StructureDiscoverer\Collections\AttributeCollection;
use Spatie\StructureDiscoverer\Collections\UsageCollection;
use Spatie\StructureDiscoverer\Enums\DiscoveredStructureType;

/**
 * @property array<string> $extends
 * @property array<DiscoveredAttribute> $attributes
 * @property ?array<string> $extendsChain
 */
class DiscoveredInterface extends DiscoveredData
{
    public function __construct(
        string $name,
        string $file,
        string $namespace,
        public array $extends,
        public array $attributes,
        public ?array $extendsChain = null,
    ) {
        parent::__construct($name, $namespace, $file);
    }

    public function getType(): DiscoveredStructureType
    {
        return DiscoveredStructureType::Interface;
    }
}
