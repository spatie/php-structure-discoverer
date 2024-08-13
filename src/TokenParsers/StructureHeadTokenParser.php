<?php

namespace Spatie\StructureDiscoverer\TokenParsers;

use Spatie\StructureDiscoverer\Collections\TokenCollection;
use Spatie\StructureDiscoverer\Collections\UsageCollection;
use Spatie\StructureDiscoverer\Data\StructureHeadData;

class StructureHeadTokenParser
{
    public function __construct(
        protected ReferenceListTokenParser $classListResolver = new ReferenceListTokenParser(),
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

            if ($tokens->get($index)->is(T_EXTENDS)) {
                $extends = $this->classListResolver->execute(
                    $index + 1,
                    $tokens,
                    $namespace,
                    $usages,
                );
            }

            if ($tokens->get($index)->is(T_IMPLEMENTS)) {
                $implements = $this->classListResolver->execute(
                    $index + 1,
                    $tokens,
                    $namespace,
                    $usages,
                );
            }

            if (! $tokens->get($index)->is([
                    T_EXTENDS,
                    T_IMPLEMENTS,
                    T_STRING,
                    T_NAME_FULLY_QUALIFIED,
                    T_NAME_FULLY_QUALIFIED,
                    T_NAME_RELATIVE,
                    T_NAME_QUALIFIED,
                ]) && $tokens->get($index)->text !== ':') {
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
