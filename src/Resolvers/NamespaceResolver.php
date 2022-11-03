<?php

namespace Spatie\LaravelAutoDiscoverer\Resolvers;

use Illuminate\Support\Collection;
use Spatie\LaravelAutoDiscoverer\Collections\TokenCollection;
use Spatie\LaravelAutoDiscoverer\Data\Token;
use Spatie\LaravelAutoDiscoverer\Data\Usage;

class NamespaceResolver
{
    public function execute(int $index, TokenCollection $tokens): string
    {
        $token = $tokens->get($index);

        if (defined('T_NAME_QUALIFIED') && $token && $token->isType(T_NAME_QUALIFIED)) {
            return $token->value;
        }

        $parts = [];

        while (true) {
            $token = $tokens->get($index);
            $index++;

            if (! $tokens->has($index) || ! $token->isType(T_NS_SEPARATOR, T_STRING)) {
                break;
            }

            $parts[] = $token->value;
        }

        return implode('', $parts);
    }
}
