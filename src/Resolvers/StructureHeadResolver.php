<?php

namespace Spatie\LaravelAutoDiscoverer\Resolvers;

use Illuminate\Support\Collection;
use Spatie\LaravelAutoDiscoverer\Collections\TokenCollection;
use Spatie\LaravelAutoDiscoverer\Collections\UsageCollection;
use Spatie\LaravelAutoDiscoverer\Data\StructureHeadData;

class StructureHeadResolver
{
    public function __construct(
        protected ReferenceListResolver $classListResolver,
    ) {
    }

    public function execute(
        int $index,
        TokenCollection $tokens,
        string $namespace,
        UsageCollection $usages,
    ): StructureHeadData {
        $extends = [];
        $implements = [];

        while (true) {
            if ($tokens->has($index) === false) {
                break;
            }

            if ($tokens->get($index)->isType(T_EXTENDS)) {
                $extends = $this->classListResolver->execute(
                    $index + 1,
                    $tokens,
                    $namespace,
                    $usages,
                );
            }

            if ($tokens->get($index)->isType(T_IMPLEMENTS)) {
                $implements = $this->classListResolver->execute(
                    $index + 1,
                    $tokens,
                    $namespace,
                    $usages,
                );
            }

            if (! $tokens->get($index)->isStructureHeadType()) {
                break;
            }

            $index++;
        }

        return new StructureHeadData(
            extends: $extends,
            implements: $implements,
        );
    }
}
