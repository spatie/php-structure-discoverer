<?php

namespace Spatie\StructureDiscoverer\TokenParsers;

use Exception;
use PhpToken;
use Spatie\StructureDiscoverer\Collections\UsageCollection;

class ReferenceTokenParser
{
    public function execute(
        PhpToken $token,
        string $namespace,
        UsageCollection $usages,
    ): string {
        if ($token->is(T_NAME_FULLY_QUALIFIED)) {
            return ltrim($token->text, '\\');
        }

        if ($token->is(T_NAME_QUALIFIED)) {
            return "{$namespace}\\{$token->text}";
        }

        if ($token->is(T_STRING)) {
            return $usages->findFcqnForIdentifier(
                $token->text,
                $namespace
            );
        }

        if ($token->is(T_NAME_RELATIVE)) {
            return str_replace('namespace', $namespace, $token->text);
        }

        throw new Exception('Unknown token type');
    }
}
