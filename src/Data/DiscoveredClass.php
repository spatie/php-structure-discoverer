<?php

namespace Spatie\StructureDiscoverer\Data;

use ReflectionAttribute;
use ReflectionClass;
use Spatie\StructureDiscoverer\Enums\DiscoveredStructureType;
use Spatie\StructureDiscoverer\Exceptions\InvalidReflection;

/**
 * @property array<string> $implements
 * @property array<DiscoveredAttribute> $attributes
 * @property ?array<string> $extendsChain
 * @property ?array<string> $implementsChain
 */
class DiscoveredClass extends DiscoveredStructure
{
    public function __construct(
        string $name,
        string $file,
        string $namespace,
        public bool $isFinal,
        public bool $isAbstract,
        public bool $isReadonly,
        public ?string $extends,
        public array $implements,
        public array $attributes,
        public ?array $extendsChain = null,
        public ?array $implementsChain = null,
    ) {
        parent::__construct($name, $file, $namespace);
    }

    public function getType(): DiscoveredStructureType
    {
        return DiscoveredStructureType::ClassDefinition;
    }

    public static function fromReflection(ReflectionClass $reflection): DiscoveredStructure
    {
        if ($reflection->isTrait() || $reflection->isInterface() || $reflection->isEnum()) {
            throw InvalidReflection::expectedClass();
        }

        $implements = array_values($reflection->getInterfaceNames());

        $extends = $reflection->getParentClass() !== false
            ? $reflection->getParentClass()->getName()
            : null;

        return new self(
            name: $reflection->getShortName(),
            file: $reflection->getFileName(),
            namespace: $reflection->getNamespaceName(),
            isFinal: $reflection->isFinal(),
            isAbstract: $reflection->isAbstract(),
            isReadonly: version_compare(phpversion(), '8.2', '>=') ? $reflection->isReadonly() : false,
            extends: $extends,
            implements: $implements,
            attributes: array_map(
                fn (ReflectionAttribute $reflectionAttribute) => DiscoveredAttribute::fromReflection($reflectionAttribute),
                $reflection->getAttributes()
            ),
            extendsChain: array_values(class_parents($reflection->getName())),
            implementsChain: $implements
        );
    }
}
