<?php

namespace Spatie\StructureDiscoverer\Data;

use ReflectionAttribute;
use ReflectionClass;
use Spatie\StructureDiscoverer\Enums\DiscoveredStructureType;
use Spatie\StructureDiscoverer\Exceptions\InvalidReflection;

class DiscoveredInterface extends DiscoveredStructure
{
    /**
     * @param array<string> $extends
     * @param array<DiscoveredAttribute> $attributes
     * @param ?array<string> $extendsChain
     */
    public function __construct(
        string $name,
        string $file,
        string $namespace,
        public array $extends,
        public array $attributes,
        public ?array $extendsChain = null,
    ) {
        parent::__construct($name, $file, $namespace);
    }

    public function getType(): DiscoveredStructureType
    {
        return DiscoveredStructureType::Interface;
    }

    /**
     * @param ReflectionClass<object> $reflection
     */
    public static function fromReflection(ReflectionClass $reflection): DiscoveredStructure
    {
        if (! $reflection->isInterface()) {
            throw InvalidReflection::expectedInterface();
        }

        $extends = array_values($reflection->getInterfaceNames());

        return new self(
            name: $reflection->getShortName(),
            file: $reflection->getFileName(),
            namespace: $reflection->getNamespaceName(),
            extends: $extends,
            attributes: array_map(
                fn (ReflectionAttribute $reflectionAttribute) => DiscoveredAttribute::fromReflection($reflectionAttribute),
                $reflection->getAttributes()
            ),
            extendsChain: $extends
        );
    }
}
