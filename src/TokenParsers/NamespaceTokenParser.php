<?php

namespace Spatie\StructureDiscoverer\TokenParsers;

use Spatie\StructureDiscoverer\Collections\TokenCollection;

class NamespaceTokenParser
{
    public function execute(int $index, TokenCollection $tokens): string
    {
        $token = $tokens->get($index);

        if (defined('T_NAME_QUALIFIED') && $token && $token->is(T_NAME_QUALIFIED)) {
            return $token->text;
        }

        $parts = [];

        do {
            $token = $tokens->get($index);

            if ($token === null || ! $token->is(T_STRING)) {
                break;
            }

            $parts[] = $token->text;
            $index++;
        } while ($index < count($tokens));

        return implode('', $parts);
    }
}
