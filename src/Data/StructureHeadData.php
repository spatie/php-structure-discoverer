<?php

namespace Spatie\LaravelAutoDiscoverer\Data;

class StructureHeadData
{
    public function __construct(
        public array $extends,
        public array $implements,
    ) {
    }
}
