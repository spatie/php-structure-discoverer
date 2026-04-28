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
        return in_array(T_FINAL, $this->classModifiers($index, $tokens), true);
    }

    protected function isClassReadonly(
        int $index,
        TokenCollection $tokens,
    ): bool {
        return defined('T_READONLY')
            && in_array(T_READONLY, $this->classModifiers($index, $tokens), true);
    }

    protected function isClassAbstract(
        int $index,
        TokenCollection $tokens,
    ): bool {
        return in_array(T_ABSTRACT, $this->classModifiers($index, $tokens), true);
    }

    /**
     * Collects modifier token ids preceding the class keyword.
     *
     * $index points at the class name. $index - 1 is T_CLASS, so we walk
     * backwards from $index - 2 collecting consecutive modifier tokens.
     *
     * @return array<int>
     */
    private function classModifiers(int $index, TokenCollection $tokens): array
    {
        $allowed = defined('T_READONLY')
            ? [T_ABSTRACT, T_FINAL, T_READONLY]
            : [T_ABSTRACT, T_FINAL];

        $modifiers = [];

        for ($i = $index - 2; $i >= 0; $i--) {
            $token = $tokens->get($i);

            if ($token === null || ! $token->is($allowed)) {
                break;
            }

            $modifiers[] = $token->id;
        }

        return $modifiers;
    }
}
