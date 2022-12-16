<?php

namespace Spatie\StructureDiscoverer\TokenParsers;

use Spatie\StructureDiscoverer\Collections\TokenCollection;
use Spatie\StructureDiscoverer\Collections\UsageCollection;
use Spatie\StructureDiscoverer\Data\DiscoveredAttribute;

class AttributeTokenParser
{
    public function __construct(
        protected ReferenceTokenParser $referenceTokenResolver = new ReferenceTokenParser(),
    ) {
    }

    /** @return array<DiscoveredAttribute> */
    public function execute(
        int $index,
        TokenCollection $tokens,
        string $namespace,
        UsageCollection $usages,
    ): array {
        $attributes = [];

        $parenthesisCount = 0;

        do {
            $token = $tokens->get($index);

            if ($token->is(T_STRING) && $parenthesisCount === 0) {
                $attributes[] = new DiscoveredAttribute(
                    $this->referenceTokenResolver->execute($token, $namespace, $usages)
                );
            }

            if ($token->is(ord('('))) {
                $parenthesisCount++;
            }

            if ($token->is(ord(')'))) {
                $parenthesisCount--;
            }

            if ($token->is(ord(']'))) {
                break;
            }

            $index++;
        } while ($index < count($tokens));

        return $attributes;
    }
}
