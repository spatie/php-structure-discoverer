<?php

namespace Spatie\LaravelAutoDiscoverer\Resolvers;

use Illuminate\Support\Collection;
use Spatie\LaravelAutoDiscoverer\Collections\TokenCollection;
use Spatie\LaravelAutoDiscoverer\Data\Token;
use Spatie\LaravelAutoDiscoverer\Data\Usage;

class UseResolver
{
    /**
     * @return Usage[]
     */
    public function execute(int $index, TokenCollection $tokens): array
    {
        $usages = [];

        if (! $tokens->get($index)->isType(T_NAME_QUALIFIED, T_STRING)) {
            return $usages;
        }

        $usage = new Usage($tokens->get($index)->value);

        $nextUseIndex = $index + 1;

        if ($tokens->has($index + 2)
            && $tokens->get($index + 1)->isType(T_AS)
            && $tokens->get($index + 2)->isType(T_STRING)
        ) {
            $usage->name = $tokens->get($index + 2)->value;

            $nextUseIndex += 2;
        }

        $usages[] = $usage;

        if ($tokens->has($nextUseIndex) && $tokens->get($nextUseIndex)->isType(T_NAME_QUALIFIED, T_STRING)) {
            array_push($usages, ...$this->execute($nextUseIndex, $tokens));
        }

        return $usages;
    }
}
