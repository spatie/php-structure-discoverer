<?php

namespace Spatie\LaravelAutoDiscoverer\Resolvers;

use _PHPStan_59fb0a3b2\Nette\Neon\Exception;
use Illuminate\Support\Collection;
use Spatie\LaravelAutoDiscoverer\Collections\TokenCollection;
use Spatie\LaravelAutoDiscoverer\Collections\UsageCollection;
use Spatie\LaravelAutoDiscoverer\Data\DiscoveredEnum;
use Spatie\LaravelAutoDiscoverer\Data\Token;
use Spatie\LaravelAutoDiscoverer\Enums\DiscoveredEnumType;

class DiscoveredEnumResolver
{
    public function __construct(
        protected StructureHeadResolver $structureHeadResolver,
    ) {
    }

    function execute(
        int $index,
        TokenCollection $tokens,
        string $namespace,
        UsageCollection $usages,
        string $file
    ): DiscoveredEnum {
        $head = $this->structureHeadResolver->execute($index, $tokens, $namespace, $usages);

        return new DiscoveredEnum(
            $tokens->get($index)->value,
            $namespace,
            $file,
            $this->resolveType($index, $tokens),
            $head->implements,
        );
    }

    protected function resolveType(
        int $index,
        TokenCollection $tokens,
    ): DiscoveredEnumType {
        $typeToken = $tokens->get($index + 1);

        if ($typeToken === null
            || ! $typeToken->isType(T_STRING)
            || ! in_array($typeToken->value, ['int', 'string'])
        ) {
            return DiscoveredEnumType::Unit;
        }

        if ($typeToken->value === 'int') {
            return DiscoveredEnumType::Int;
        }

        if ($typeToken->value === 'string') {
            return DiscoveredEnumType::String;
        }

        throw new Exception('Unknown enum type');
    }
}
