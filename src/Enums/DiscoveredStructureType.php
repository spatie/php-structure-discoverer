<?php

namespace Spatie\StructureDiscoverer\Enums;

use Exception;
use PhpToken;
use Spatie\StructureDiscoverer\Data\DiscoveredClass;
use Spatie\StructureDiscoverer\Data\DiscoveredEnum;
use Spatie\StructureDiscoverer\Data\DiscoveredInterface;
use Spatie\StructureDiscoverer\Data\DiscoveredTrait;
use Spatie\StructureDiscoverer\Data\Token;

enum DiscoveredStructureType
{
    case ClassDefinition;
    case Enum;
    case Trait;
    case Interface;

    public static function fromToken(
        PhpToken $token
    ): ?self {
        return match ($token->id) {
            T_CLASS => self::ClassDefinition,
            T_ENUM => self::Enum,
            T_INTERFACE => self::Interface,
            T_TRAIT => self::Trait,
            default => null,
        };
    }

    /** @return class-string<\Spatie\StructureDiscoverer\Data\DiscoveredData> */
    public function getDataClass(): string
    {
        return match ($this) {
            self::ClassDefinition => DiscoveredClass::class,
            self::Enum => DiscoveredEnum::class,
            self::Interface => DiscoveredInterface::class,
            self::Trait => DiscoveredTrait::class,
            default => throw new Exception('Unknown type'),
        };
    }
}