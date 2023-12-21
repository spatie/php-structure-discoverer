<?php

namespace Spatie\StructureDiscoverer\StructureParsers;

use Spatie\StructureDiscoverer\TokenParsers\MultiFileTokenParser;

class PhpTokenStructureParser implements StructureParser
{
    public function execute(array $filenames): array
    {
        return (new MultiFileTokenParser())->execute($filenames);
    }
}
