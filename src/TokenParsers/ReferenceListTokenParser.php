<?php

namespace Spatie\StructureDiscoverer\TokenParsers;

use Spatie\StructureDiscoverer\Collections\TokenCollection;
use Spatie\StructureDiscoverer\Collections\UsageCollection;

class ReferenceListTokenParser
{
    public function __construct(
        protected ReferenceTokenParser $referenceTokenResolver = new ReferenceTokenParser(),
    ) {
    }

    /** @return array<string> */
    public function execute(
        int $index,
        TokenCollection $tokens,
        string $namespace,
        UsageCollection $usages,
    ): array {
        $classes = [];

        do {
            $token = $tokens->get($index);

            if ($token === null) {
                break;
            }

            $classes[] = $this->referenceTokenResolver->execute(
                $token,
                $namespace,
                $usages,
            );

            if (! $tokens->get($index + 1)->is(ord(','))) {
                break;
            }

            $index += 2;
        } while ($tokens->get($index)?->is([
            T_NAME_FULLY_QUALIFIED,
            T_NAME_QUALIFIED,
            T_STRING,
            T_NAME_RELATIVE,
        ]));

        return $classes;
    }
}
