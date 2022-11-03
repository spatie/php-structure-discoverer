<?php

namespace Spatie\LaravelAutoDiscoverer\Resolvers;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Spatie\LaravelAutoDiscoverer\Collections\TokenCollection;
use Spatie\LaravelAutoDiscoverer\Collections\UsageCollection;
use Spatie\LaravelAutoDiscoverer\Data\Token;
use Stringable;

class ReferenceListResolver
{
    public function __construct(
        protected ReferenceTokenResolver $referenceTokenResolver,
    ) {
    }

    public function execute(
        int $index,
        TokenCollection $tokens,
        string $namespace,
        UsageCollection $usages,
    ): array|string {
        $classes = [];

        while (true) {
            $currentToken = $tokens->get($index);

            if ($currentToken === null) {
                break;
            }

            if (! $currentToken->isType(
                T_NAME_FULLY_QUALIFIED,
                T_NAME_QUALIFIED,
                T_STRING,
                T_NAME_RELATIVE
            )) {
                break;
            }

            $classes[] = $this->referenceTokenResolver->execute(
                $currentToken,
                $namespace,
                $usages,
            );

            $index++;
        }

        return $classes;
    }
}
