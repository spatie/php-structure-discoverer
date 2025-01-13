<?php

namespace Spatie\StructureDiscoverer\TokenParsers;

use Spatie\StructureDiscoverer\Collections\TokenCollection;
use Spatie\StructureDiscoverer\Collections\UsageCollection;
use Spatie\StructureDiscoverer\Data\DiscoveredStructure;
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

        $tokens = TokenCollection::fromCode($contents);

        if ($tokens->count() === 0) {
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

                if (
                    $type === DiscoveredStructureType::ClassDefinition
                    && $this->isAnonymousClass($tokens, $index)
                ) {
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

    private function isAnonymousClass(TokenCollection $tokens, int $index): bool
    {
        $prevIndex = $index - 1;

        // find the previous non-whitespace token
        while ($prevIndex >= 0 && $tokens->get($prevIndex)->is(T_WHITESPACE)) {
            $prevIndex--;
        }

        // if the token before T_CLASS is T_NEW, it's an anonymous class
        if ($prevIndex >= 0 && $tokens->get($prevIndex)->is(T_NEW)) {
            return true;
        }

        return false;
    }
}
