<?php

namespace Spatie\StructureDiscoverer\Data;

use Spatie\StructureDiscoverer\Enums\DiscoveredStructureType;

abstract class DiscoveredStructure
{
    public function __construct(
        public string $name,
        public string $file,
        public string $namespace,
    ) {
    }

    abstract public function getType(): DiscoveredStructureType;

    public function getFcqn(): string
    {
        return empty($this->namespace) ? $this->name : "{$this->namespace}\\{$this->name}";
    }
}
