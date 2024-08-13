<?php

namespace Spatie\StructureDiscoverer\Data;

use Exception;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionEnum;
use Spatie\StructureDiscoverer\Enums\DiscoveredEnumType;
use Spatie\StructureDiscoverer\Enums\DiscoveredStructureType;
use Spatie\StructureDiscoverer\Exceptions\InvalidReflection;

class DiscoveredEnum extends DiscoveredStructure
{
    /**
     * @param array<string> $implements
     * @param array<DiscoveredAttribute> $attributes
     * @param ?array<string> $implementsChain
     */
    public function __construct(
        public string $name,
        public string $file,
        public string $namespace,
        public DiscoveredEnumType $type,
        public array $implements,
        public array $attributes,
        public ?array $implementsChain = null,
    ) {
        parent::__construct($name, $file, $namespace);
    }

    public function getType(): DiscoveredStructureType
    {
        return DiscoveredStructureType::Enum;
    }

    /**
     * @param ReflectionClass<object> $reflection
     */
    public static function fromReflection(ReflectionClass $reflection): DiscoveredStructure
    {
        if (! $reflection instanceof ReflectionEnum) {
            throw InvalidReflection::create(ReflectionEnum::class, $reflection);
        }

        if (! $reflection->isEnum()) {
            throw InvalidReflection::expectedEnum();
        }

        $type = match (true) {
            $reflection->isBacked() === false => DiscoveredEnumType::Unit,
            $reflection->isBacked() === true && (string) $reflection->getBackingType() === 'string' => DiscoveredEnumType::String,
            $reflection->isBacked() === true && (string) $reflection->getBackingType() === 'int' => DiscoveredEnumType::Int,
            default => throw new Exception('Unknown enum type')
        };

        $implements = array_values($reflection->getInterfaceNames());

        return new self(
            name: $reflection->getShortName(),
            file: $reflection->getFileName(),
            namespace: $reflection->getNamespaceName(),
            type: $type,
            implements: $implements,
            attributes: array_map(
                fn (ReflectionAttribute $reflectionAttribute) => DiscoveredAttribute::fromReflection($reflectionAttribute),
                $reflection->getAttributes()
            ),
            implementsChain: $implements
        );
    }
}
