<?php

namespace Spatie\StructureDiscoverer\TokenParsers;

use Spatie\StructureDiscoverer\Collections\TokenCollection;
use Spatie\StructureDiscoverer\Data\Usage;

class UseTokenParser
{
    /**
     * @return Usage[]
     */
    public function execute(int $index, TokenCollection $tokens): array
    {
        if ($this->isGroupUse($index, $tokens)) {
            return $this->executeGroupUse($index, $tokens);
        }

        $usages = [];

        do {
            if ($tokens->get($index + 1)->is(T_AS)) {
                $usages[] = new Usage(
                    $tokens->get($index)->text,
                    $tokens->get($index + 2)->text
                );

                $index += 3;
            } else {
                $usages[] = new Usage($tokens->get($index)->text);

                $index += 1;
            }

            if ($tokens->get($index)?->is(ord(','))) {
                $index += 1;

                continue;
            }

            break;
        } while ($tokens->get($index)->is([T_NAME_QUALIFIED, T_STRING]));

        return $usages;
    }

    private function isGroupUse(int $index, TokenCollection $tokens): bool
    {
        if ($tokens->get($index)?->is([T_FUNCTION, T_CONST])) {
            $index += 1;
        }

        // PHP tokenizes `A\B\{` as the prefix name, a separate `\` and `{`
        return $tokens->get($index + 1)?->is(T_NS_SEPARATOR) === true
            && $tokens->get($index + 2)?->is(ord('{')) === true;
    }

    /**
     * @return Usage[]
     */
    private function executeGroupUse(int $index, TokenCollection $tokens): array
    {
        // `use function A\{b}` and `use const A\{B}` don't import classes
        if ($tokens->get($index)->is([T_FUNCTION, T_CONST])) {
            return [];
        }

        $prefix = $tokens->get($index)->text;
        $index += 3;

        $usages = [];

        while (! $tokens->get($index)->is(ord('}'))) {
            $importsClass = ! $tokens->get($index)->is([T_FUNCTION, T_CONST]);

            if (! $importsClass) {
                $index += 1;
            }

            $name = $tokens->get($index)->text;
            $alias = null;

            if ($tokens->get($index + 1)->is(T_AS)) {
                $alias = $tokens->get($index + 2)->text;
                $index += 2;
            }

            if ($importsClass) {
                $usages[] = new Usage("{$prefix}\\{$name}", $alias);
            }

            $index += 1;

            if ($tokens->get($index)->is(ord(','))) {
                $index += 1;
            }
        }

        return $usages;
    }
}
