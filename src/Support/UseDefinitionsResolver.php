<?php

namespace Spatie\StructureDiscoverer\Support;

use Spatie\StructureDiscoverer\Collections\TokenCollection;
use Spatie\StructureDiscoverer\Collections\UsageCollection;
use Spatie\StructureDiscoverer\TokenParsers\UseTokenParser;

class UseDefinitionsResolver
{
    public function __construct(
        protected UseTokenParser $useResolver = new UseTokenParser(),
    ) {
    }

    public function execute(string $filename): UsageCollection
    {
        // This is a feature for laravel-data and typescript-transformer

        $usages = new UsageCollection();

        $code = file_get_contents($filename);

        if ($code === false) {
            return new UsageCollection();
        }

        $tokens = TokenCollection::fromCode($code);

        $index = 0;

        while ($tokens->has($index)) {
            $token = $tokens->get($index);

            if ($token->is(T_USE)) {
                $usages->add(...$this->useResolver->execute($index + 1, $tokens));

                // skip the rest of the statement, so the `function` in `use function` doesn't end the loop
                $index = $this->findStatementEnd($index, $tokens) + 1;

                continue;
            }

            if ($token->is([T_CLASS, T_INTERFACE, T_TRAIT, T_FUNCTION])) {
                break;
            }

            $index++;
        }

        return $usages;
    }

    private function findStatementEnd(int $index, TokenCollection $tokens): int
    {
        while ($tokens->has($index) && ! $tokens->get($index)->is(ord(';'))) {
            $index++;
        }

        return $index;
    }
}
