<?php

namespace Spatie\StructureDiscoverer\TokenParsers;

use Spatie\StructureDiscoverer\Collections\TokenCollection;
use Spatie\StructureDiscoverer\Collections\UsageCollection;
use Spatie\StructureDiscoverer\Data\DiscoveredAttribute;
use Spatie\StructureDiscoverer\Data\DiscoveredEnum;
use Spatie\StructureDiscoverer\Enums\DiscoveredEnumType;

class DiscoveredEnumTokenParser
{
    public function __construct(
        protected StructureHeadTokenParser $structureHeadResolver = new StructureHeadTokenParser(),
    ) {
    }

    /**
     * @param DiscoveredAttribute[] $attributes
     */
    public function execute(
        int $index,
        TokenCollection $tokens,
        string $namespace,
        UsageCollection $usages,
        array $attributes,
        string $file,
    ): DiscoveredEnum {
        $head = $this->structureHeadResolver->execute($index, $tokens, $namespace, $usages);

        return new DiscoveredEnum(
            $tokens->get($index)->text,
            $file,
            $namespace,
            $this->resolveType($index, $tokens),
            $head->implements,
            $attributes,
        );
    }

    protected function resolveType(
        int $index,
        TokenCollection $tokens,
    ): DiscoveredEnumType {
        $typeToken = $tokens->get($index + 2);

        if ($typeToken === null
            || ! $typeToken->is(T_STRING)
            || ! in_array($typeToken->text, ['int', 'string'])
        ) {
            return DiscoveredEnumType::Unit;
        }

        if ($typeToken->text === 'int') {
            return DiscoveredEnumType::Int;
        }

        if ($typeToken->text === 'string') {
            return DiscoveredEnumType::String;
        }
    }
}
