<?php

namespace Spatie\StructureDiscoverer\Enums;

use Symfony\Component\Finder\Finder;

enum Sort
{
    case Name;
    case Size;
    case Type;
    case Extension;
    case ChangedTime;
    case ModifiedTime;
    case AccessedTime;
    case CaseInsensitiveName;

    public function apply(Finder $finder): void
    {
        match ($this) {
            self::Name => $finder->sortByName(),
            self::Size => $finder->sortBySize(),
            self::Type => $finder->sortByType(),
            self::Extension => $finder->sortByExtension(),
            self::ChangedTime => $finder->sortByChangedTime(),
            self::ModifiedTime => $finder->sortByModifiedTime(),
            self::AccessedTime => $finder->sortByAccessedTime(),
            self::CaseInsensitiveName => $finder->sortByCaseInsensitiveName(),
        };
    }
}
