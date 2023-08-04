<?php

namespace Spatie\StructureDiscoverer\StructureParsers;

use Spatie\StructureDiscoverer\Data\DiscoveredStructure;

interface StructureParser
{
    /**
     * @param array<string> $filenames
     *
     * @return array<DiscoveredStructure>
     */
    public function execute(array $filenames): array;
}
