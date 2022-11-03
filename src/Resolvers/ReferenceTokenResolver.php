<?php

namespace Spatie\LaravelAutoDiscoverer\Resolvers;

use Exception;
use Spatie\LaravelAutoDiscoverer\Collections\UsageCollection;
use Spatie\LaravelAutoDiscoverer\Data\Token;

class ReferenceTokenResolver
{
    public function execute(
        Token $token,
        string $namespace,
        UsageCollection $usages,
    ): string {
        if ($token->isType(T_NAME_FULLY_QUALIFIED)) {
            return ltrim($token->value, '\\');
        }

        if ($token->isType(T_NAME_QUALIFIED)) {
            return "{$namespace}\\{$token->value}";
        }

        if ($token->isType(T_STRING)) {
            return $usages->findFcqnForIdentifier(
                $token->value,
                $namespace
            );
        }

        if ($token->isType(T_NAME_RELATIVE)) {
            return str_replace('namespace', $namespace, $token->value);
        }

        throw new Exception('Unknown token type');
    }
}
