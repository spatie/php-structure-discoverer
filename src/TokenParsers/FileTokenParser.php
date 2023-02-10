<?php

namespace Spatie\StructureDiscoverer\TokenParsers;

use Illuminate\Support\Collection;
use ParseError;
use PhpToken;
use Spatie\StructureDiscoverer\Collections\TokenCollection;
use Spatie\StructureDiscoverer\Collections\UsageCollection;
use Spatie\StructureDiscoverer\Data\DiscoveredStructure;
use Spatie\StructureDiscoverer\Data\Token;
use Spatie\StructureDiscoverer\Enums\DiscoveredStructureType;
use Spatie\StructureDiscoverer\Exceptions\CouldNotParseFile;
use Throwable;

class FileTokenParser
{
    public function __construct(
        protected UseTokenParser $useResolver = new UseTokenParser(),
        protected NamespaceTokenParser $namespaceResolver = new NamespaceTokenParser(),
        protected DiscoveredDataTokenParser $discoveredDataResolver = new DiscoveredDataTokenParser(),
        protected AttributeTokenParser $attributeResolver = new AttributeTokenParser(),
    ) {
    }

    /** @return array<DiscoveredStructure> */
    public function execute(
        string $filename,
        string $contents,
    ): array {
        $found = [];

        try {
            /** @var TokenCollection $tokens */
            $tokens = collect(PhpToken::tokenize($contents, TOKEN_PARSE))
                ->reject(fn (PhpToken $token) => $token->is([T_COMMENT, T_DOC_COMMENT, T_WHITESPACE]))
                ->values()
                ->pipe(fn (Collection $collection): TokenCollection => new TokenCollection($collection->all()));
        } catch (ParseError) {
            return [];
        }

        $currentNamespace = '';
        $usages = new UsageCollection();
        $attributes = [];
        $structureDefined = false;

        $index = 0;

        try {
            do {
                if ($tokens->get($index)->is(T_NAMESPACE)) {
                    $index++; // move to token after 'namespace'

                    $currentNamespace = $this->namespaceResolver->execute($index, $tokens);

                    continue;
                }

                if ($tokens->get($index)->is(T_USE) && $structureDefined === false) {
                    $usages->add(...$this->useResolver->execute($index + 1, $tokens));
                }

                if ($tokens->get($index)->is(T_ATTRIBUTE)) {
                    $attributes = [
                        ...$attributes, ...$this->attributeResolver->execute(
                            $index + 1,
                            $tokens,
                            $currentNamespace,
                            $usages,
                        ),
                    ];
                }

                $type = DiscoveredStructureType::fromToken($tokens->get($index));

                if ($type === null) {
                    $index++;

                    continue;
                }

                $discoveredStructure = $this->discoveredDataResolver->execute(
                    $index + 1,
                    $tokens,
                    $currentNamespace,
                    $usages,
                    $attributes,
                    $type,
                    $filename,
                );

                $found[$discoveredStructure->getFcqn()] = $discoveredStructure;
                $attributes = [];

                $structureDefined = true;

                $index++;
            } while ($index < count($tokens));
        } catch (Throwable $throwable) {
            throw new CouldNotParseFile($filename, $throwable);
        }

        return $found;
    }
}
