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

        foreach ($tokens as $i => $token) {
            if ($token->is([T_COMMENT, T_DOC_COMMENT, T_WHITESPACE])) {
                continue;
            }

            if ($token->is(T_USE)) {
                $usages->add(...$this->useResolver->execute($i + 1, $tokens));
            }

            if ($token->is([T_CLASS, T_INTERFACE, T_TRAIT, T_FUNCTION])) {
                break;
            }
        }

        return $usages;
    }
}
