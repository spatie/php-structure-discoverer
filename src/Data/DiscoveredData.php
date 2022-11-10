<?php

namespace Spatie\StructureDiscoverer\Data;

use Spatie\StructureDiscoverer\Enums\DiscoveredStructureType;

abstract class DiscoveredData
{
    public function __construct(
        public string $name,
        public string $namespace,
        public string $file,
    ) {
    }

    abstract public function getType(): DiscoveredStructureType;

    public function getFcqn(): string
    {
        return empty($this->namespace) ? $this->name : "{$this->namespace}\\{$this->name}";
    }
}
