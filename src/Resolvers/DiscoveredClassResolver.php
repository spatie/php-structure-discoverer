<?php

namespace Spatie\LaravelAutoDiscoverer\Resolvers;

use Illuminate\Support\Collection;
use Spatie\LaravelAutoDiscoverer\Collections\TokenCollection;
use Spatie\LaravelAutoDiscoverer\Collections\UsageCollection;
use Spatie\LaravelAutoDiscoverer\Data\DiscoveredClass;

class DiscoveredClassResolver
{
    public function __construct(
        protected StructureHeadResolver $structureHeadResolver,
    ) {
    }

    public function execute(
        int $index,
        TokenCollection $tokens,
        string $namespace,
        UsageCollection $usages,
        string $file
    ): DiscoveredClass {
        $head = $this->structureHeadResolver->execute($index, $tokens, $namespace, $usages);

        return new DiscoveredClass(
            name: $tokens->get($index)->value,
            file: $file,
            namespace: $namespace,
            isFinal: $this->isClassFinal($index, $tokens),
            isAbstract: $this->isClassAbstract($index, $tokens),
            isReadonly: $this->isClassReadonly($index, $tokens),
            extends: $head->extends[0] ?? null,
            implements: $head->implements,
            attributes: []
        );
    }

    protected function isClassFinal(
        int $index,
        TokenCollection $tokens,
    ): bool {
        $token = $tokens->get($index - 2);

        return $token && $token->isType(T_FINAL);
    }

    protected function isClassReadonly(
        int $index,
        TokenCollection $tokens,
    ): bool {
        $token = $tokens->get($index - 2);

        return defined(T_READONLY) && $token && $token->isType(T_READONLY);
    }

    protected function isClassAbstract(
        int $index,
        TokenCollection $tokens,
    ): bool {
        $token = $tokens->get($index - 2);

        return $token && $token->isType(T_ABSTRACT);
    }
}
