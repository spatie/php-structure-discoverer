<?php

namespace Spatie\StructureDiscoverer\TokenParsers;

use Spatie\StructureDiscoverer\TokenParsers\FileTokenParser;

class MultiFileTokenParser
{
    public function execute(array $filenames): array
    {
        $resolver = new FileTokenParser();

        $found = [];

        foreach ($filenames as $filename) {
            $contents = file_get_contents($filename) ?: '';

            foreach ($resolver->execute($filename, $contents) as $fqcn => $structure) {
                $found[$fqcn] = $structure;
            }
        }

        return $found;
    }
}
