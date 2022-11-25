<?php

namespace Spatie\StructureDiscoverer\TokenParsers;

use Illuminate\Support\Facades\File;
use Spatie\StructureDiscoverer\TokenParsers\FileTokenParser;

class MultiFileTokenParser
{
    public function __construct(
        protected FileTokenParser $fileTokenParser = new FileTokenParser()
    )
    {
    }

    public function execute(array $filenames): array
    {
        $found = [];

        foreach ($filenames as $filename) {
            $contents = file_get_contents($filename) ?: '';

            foreach ($this->fileTokenParser->execute($filename, $contents) as $fqcn => $structure) {
                $found[$fqcn] = $structure;
            }
        }

        return $found;
    }
}
