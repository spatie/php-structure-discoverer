<?php

namespace Spatie\StructureDiscoverer\TokenParsers;

use Spatie\StructureDiscoverer\Collections\TokenCollection;
use Spatie\StructureDiscoverer\Collections\UsageCollection;
use Spatie\StructureDiscoverer\Data\DiscoveredAttribute;
use Spatie\StructureDiscoverer\Data\DiscoveredClass;

class DiscoveredClassTokenParser
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
        string $file
    ): DiscoveredClass {
        $head = $this->structureHeadResolver->execute($index, $tokens, $namespace, $usages);

        return new DiscoveredClass(
            name: $tokens->get($index)->text,
            file: $file,
            namespace: $namespace,
            isFinal: $this->isClassFinal($index, $tokens),
            isAbstract: $this->isClassAbstract($index, $tokens),
            isReadonly: $this->isClassReadonly($index, $tokens),
            extends: $head->extends[0] ?? null,
            implements: $head->implements,
            attributes: $attributes,
        );
    }

    protected function isClassFinal(
        int $index,
        TokenCollection $tokens,
    ): bool {
        $token = $tokens->get($index - 2);

        return $token && $token->is(T_FINAL);
    }

    protected function isClassReadonly(
        int $index,
        TokenCollection $tokens,
    ): bool {
        $token = $tokens->get($index - 2);

        return defined('T_READONLY') && $token && $token->is(T_READONLY);
    }

    protected function isClassAbstract(
        int $index,
        TokenCollection $tokens,
    ): bool {
        $token = $tokens->get($index - 2);

        return $token && $token->is(T_ABSTRACT);
    }
}
