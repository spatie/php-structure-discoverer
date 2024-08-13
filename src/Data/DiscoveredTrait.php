<?php

namespace Spatie\StructureDiscoverer\Data;

use ReflectionClass;
use Spatie\StructureDiscoverer\Enums\DiscoveredStructureType;
use Spatie\StructureDiscoverer\Exceptions\InvalidReflection;

class DiscoveredTrait extends DiscoveredStructure
{
    public function getType(): DiscoveredStructureType
    {
        return DiscoveredStructureType::Trait;
    }

    /**
     * @param ReflectionClass<object> $reflection
     */
    public static function fromReflection(ReflectionClass $reflection): DiscoveredStructure
    {
        if (! $reflection->isTrait()) {
            throw InvalidReflection::expectedTrait();
        }

        return new self(
            name: $reflection->getShortName(),
            file: $reflection->getFileName(),
            namespace: $reflection->getNamespaceName()
        );
    }
}
