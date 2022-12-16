<?php

namespace Spatie\StructureDiscoverer\Data;

class DiscoveredAttribute
{
    public function __construct(
        public string $class,
    ) {
    }
}
