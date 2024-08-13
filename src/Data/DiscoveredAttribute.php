<?php

namespace Spatie\StructureDiscoverer\Data;

use ReflectionAttribute;
use ReflectionClass;

class DiscoveredAttribute
{
    public function __construct(
        public string $class,
    ) {
    }

    /**
     * @param ReflectionAttribute<object> $reflectionAttribute
     */
    public static function fromReflection(
        ReflectionAttribute $reflectionAttribute,
    ): self {
        return new self($reflectionAttribute->getName());
    }
}
