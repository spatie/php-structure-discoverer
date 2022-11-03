<?php

namespace Spatie\LaravelAutoDiscoverer\Resolvers;

use Illuminate\Support\Collection;
use Spatie\LaravelAutoDiscoverer\Collections\TokenCollection;
use Spatie\LaravelAutoDiscoverer\Collections\UsageCollection;
use Spatie\LaravelAutoDiscoverer\Data\DiscoveredClass;
use Spatie\LaravelAutoDiscoverer\Data\DiscoveredEnum;
use Spatie\LaravelAutoDiscoverer\Data\DiscoveredInterface;
use Spatie\LaravelAutoDiscoverer\Data\DiscoveredTrait;
use Spatie\LaravelAutoDiscoverer\Enums\DiscoveredStructureType;

class DiscoveredDataResolver
{
    public function __construct(
        protected ReferenceTokenResolver $referenceTokenResolver,
        protected ReferenceListResolver $classListResolver,
        protected DiscoveredEnumResolver $discoveredEnumResolver,
        protected DiscoveredClassResolver $discoveredClassResolver,
        protected StructureHeadResolver $structureHeadResolver,
    ) {
    }

    public function execute(
        int $index,
        TokenCollection $tokens,
        string $namespace,
        UsageCollection $usages,
        DiscoveredStructureType $type,
        string $file
    ): DiscoveredInterface|DiscoveredClass|DiscoveredTrait|DiscoveredEnum {
        return match ($type) {
            DiscoveredStructureType::ClassDefinition => $this->discoveredClassResolver->execute(
                $index,
                $tokens,
                $namespace,
                $usages,
                $file
            ),
            DiscoveredStructureType::Interface => $this->resolveInterface(
                $index,
                $tokens,
                $namespace,
                $usages,
                $file
            ),
            DiscoveredStructureType::Trait => $this->resolveTrait(
                $index,
                $tokens,
                $namespace,
                $usages,
                $file
            ),
            DiscoveredStructureType::Enum => $this->discoveredEnumResolver->execute(
                $index,
                $tokens,
                $namespace,
                $usages,
                $file
            )
        };
    }

    protected function resolveInterface(
        int $index,
        TokenCollection $tokens,
        string $namespace,
        UsageCollection $usages,
        string $file
    ): DiscoveredInterface {
        $head = $this->structureHeadResolver->execute($index, $tokens, $namespace, $usages);

        return new DiscoveredInterface(
            $tokens->get($index)->value,
            $file,
            $namespace,
            $head->extends,
        );
    }

    protected function resolveTrait(
        int $index,
        TokenCollection $tokens,
        string $namespace,
        UsageCollection $usages,
        string $file
    ): DiscoveredTrait {
        return new DiscoveredTrait(
            $tokens->get($index)->value,
            $file,
            $namespace,
        );
    }
}
