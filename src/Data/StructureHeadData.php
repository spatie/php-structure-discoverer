<?php

namespace Spatie\StructureDiscoverer\Data;

class StructureHeadData
{
    public function __construct(
        public array $extends,
        public array $implements,
    ) {
    }
}
