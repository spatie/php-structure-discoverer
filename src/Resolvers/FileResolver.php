<?php

namespace Spatie\LaravelAutoDiscoverer\Resolvers;

use Illuminate\Support\Collection;
use ParseError;
use Spatie\LaravelAutoDiscoverer\Collections\TokenCollection;
use Spatie\LaravelAutoDiscoverer\Collections\UsageCollection;
use Spatie\LaravelAutoDiscoverer\Data\DiscoveredClass;
use Spatie\LaravelAutoDiscoverer\Data\DiscoveredData;
use Spatie\LaravelAutoDiscoverer\Data\DiscoveredEnum;
use Spatie\LaravelAutoDiscoverer\Data\DiscoveredInterface;
use Spatie\LaravelAutoDiscoverer\Data\DiscoveredTrait;
use Spatie\LaravelAutoDiscoverer\Data\Token;
use Spatie\LaravelAutoDiscoverer\Enums\DiscoveredStructureType;
use Spatie\LaravelAutoDiscoverer\Exceptions\CouldNotParseFile;
use Throwable;

class FileResolver
{
    public function __construct(
        protected UseResolver $useResolver,
        protected NamespaceResolver $namespaceResolver,
        protected DiscoveredDataResolver $discoveredDataResolver,
    ) {
    }

    /** @return array<DiscoveredData> */
    public function execute(
        string $filename,
        string $contents,
    ): array {
        $found = [];

        try {
            $tokens = collect(token_get_all($contents, TOKEN_PARSE))
                ->filter(fn(mixed $token) => is_array($token))
                ->filter(fn(array $token) => ! in_array($token[0], [T_COMMENT, T_DOC_COMMENT, T_WHITESPACE]))
                ->map(fn(array $token) => Token::fromToken($token))
                ->values()
                ->pipe(fn(Collection $collection): TokenCollection => new TokenCollection($collection->all()));
        } catch (ParseError $error) {
            return [];
        }

        $currentNamespace = '';
        $usages = new UsageCollection();
        $structureDefined = false;

        $index = 0;

        try {
            do {
                $token = $tokens->get($index);

                if ($token->isType(T_NAMESPACE)) {
                    $currentNamespace = $this->namespaceResolver->execute($index + 1, $tokens);
                }

                if ($token->isType(T_USE) && $structureDefined === false) {
                    $usages->add(...$this->useResolver->execute($index + 1, $tokens));
                }

                $type = DiscoveredStructureType::fromToken($token);

                if ($type === null) {
                    $index++;
                    continue;
                }

                $discoveredStructure = $this->discoveredDataResolver->execute(
                    $index + 1,
                    $tokens,
                    $currentNamespace,
                    $usages,
                    $type,
                    $filename,
                );

                $found[] = $discoveredStructure;

                $structureDefined = true;

                $index++;
            } while ($index < count($tokens));
        } catch (Throwable $throwable) {
            throw new CouldNotParseFile($filename, $throwable);
        }

        return $found;
    }
}
