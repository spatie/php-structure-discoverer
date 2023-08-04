<?php

namespace Spatie\StructureDiscoverer\Enums;

use Spatie\StructureDiscoverer\StructureParsers\FilenameReflectionParser;
use Spatie\StructureDiscoverer\TokenParsers\MultiFileTokenParser;

enum StructureParserType
{
    case Tokens;
    case Reflection;

    public function getParserClass(): string
    {
        return match ($this) {
            self::Tokens => MultiFileTokenParser::class,
            self::Reflection => FilenameReflectionParser::class,
        };
    }
}
