<?php

namespace Spatie\StructureDiscoverer\Data;

use Spatie\StructureDiscoverer\Enums\DiscoveredStructureType;

/**
 * @property array<string> $extends
 * @property array<string> $implements
 * @property array<DiscoveredAttribute> $attributes
 * @property ?array<string> $extendsChain
 * @property ?array<string> $implementsChain
 */
class DiscoveredClass extends DiscoveredStructure
{
    public function __construct(
        string $name,
        string $file,
        string $namespace,
        public bool $isFinal,
        public bool $isAbstract,
        public bool $isReadonly,
        public ?string $extends,
        public array $implements,
        public array $attributes,
        public ?array $extendsChain = null,
        public ?array $implementsChain = null,
    ) {
        parent::__construct($name, $namespace, $file);
    }

    public function getType(): DiscoveredStructureType
    {
        return DiscoveredStructureType::ClassDefinition;
    }
}
