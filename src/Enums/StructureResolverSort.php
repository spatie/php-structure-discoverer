<?php

namespace Spatie\StructureDiscoverer\Enums;

use Symfony\Component\Finder\Finder;

enum StructureResolverSort
{
    case NAME;
    case SIZE;
    case TYPE;
    case EXTENSION;
    case CHANGED_TIME;
    case MODIFIED_TIME;
    case ACCESSED_TIME;
    case CASE_INSENSITIVE_NAME;

    public function apply(Finder $finder): void
    {
        match ($this) {
            self::NAME => $finder->sortByName(),
            self::SIZE => $finder->sortBySize(),
            self::TYPE => $finder->sortByType(),
            self::EXTENSION => $finder->sortByExtension(),
            self::CHANGED_TIME => $finder->sortByChangedTime(),
            self::MODIFIED_TIME => $finder->sortByModifiedTime(),
            self::ACCESSED_TIME => $finder->sortByAccessedTime(),
            self::CASE_INSENSITIVE_NAME => $finder->sortByCaseInsensitiveName(),
        };
    }
}
