<?php

namespace Spatie\LaravelAutoDiscoverer\Enums;

use Exception;
use Spatie\LaravelAutoDiscoverer\Data\DiscoveredClass;
use Spatie\LaravelAutoDiscoverer\Data\DiscoveredEnum;
use Spatie\LaravelAutoDiscoverer\Data\DiscoveredInterface;
use Spatie\LaravelAutoDiscoverer\Data\DiscoveredTrait;
use Spatie\LaravelAutoDiscoverer\Data\Token;

enum DiscoveredStructureType
{
    case ClassDefinition;
    case Enum;
    case Trait;
    case Interface;

    public static function fromToken(
        Token $token
    ): ?self {
        return match ($token->type) {
            T_CLASS => self::ClassDefinition,
            T_ENUM => self::Enum,
            T_INTERFACE => self::Interface,
            T_TRAIT => self::Trait,
            default => null,
        };
    }

    /** @return class-string<\Spatie\LaravelAutoDiscoverer\Data\DiscoveredData> */
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
