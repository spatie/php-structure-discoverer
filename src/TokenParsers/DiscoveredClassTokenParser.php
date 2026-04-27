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
        return $this->hasModifier($index, $tokens, T_FINAL);
    }

    protected function isClassReadonly(
        int $index,
        TokenCollection $tokens,
    ): bool {
        return defined('T_READONLY') && $this->hasModifier($index, $tokens, T_READONLY);
    }

    protected function isClassAbstract(
        int $index,
        TokenCollection $tokens,
    ): bool {
        return $this->hasModifier($index, $tokens, T_ABSTRACT);
    }

    private function hasModifier(int $index, TokenCollection $tokens, int $tokenType): bool
    {
        $modifiers = [T_ABSTRACT, T_FINAL];
        if (defined('T_READONLY')) {
            $modifiers[] = T_READONLY;
        }

        // $index points to the class name (T_STRING), $index - 1 is T_CLASS.
        // Walk backwards from $index - 2 through any modifier tokens.
        for ($i = $index - 2; $i >= 0; $i--) {
            $token = $tokens->get($i);
            if ($token === null || ! $token->is($modifiers)) {
                break;
            }
            if ($token->is($tokenType)) {
                return true;
            }
        }

        return false;
    }
}
