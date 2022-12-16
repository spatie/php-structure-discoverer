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
}
