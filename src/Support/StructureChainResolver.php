<?php

namespace Spatie\StructureDiscoverer\Support;

use Spatie\StructureDiscoverer\Data\DiscoveredClass;
use Spatie\StructureDiscoverer\Data\DiscoveredEnum;
use Spatie\StructureDiscoverer\Data\DiscoveredInterface;

class StructureChainResolver
{
    /**
     * @param array<DiscoveredClass|DiscoveredEnum|DiscoveredInterface> $discovered
     */
    public function execute(array &$discovered): void
    {
        foreach ($discovered as $structure) {
            if ($structure instanceof DiscoveredClass && $structure->extendsChain === null) {
                $this->resolveExtendsChain($discovered, $structure);
            }

            if ($structure instanceof DiscoveredClass && $structure->implementsChain === null) {
                $this->resolveImplementsChain($discovered, $structure);
            }

            if ($structure instanceof DiscoveredEnum && $structure->implementsChain === null) {
                $this->resolveImplementsChain($discovered, $structure);
            }

            if ($structure instanceof DiscoveredInterface && $structure->extendsChain === null) {
                $this->resolveImplementsChain($discovered, $structure);
            }
        }
    }

    /**
     * @param array<DiscoveredClass> $discovered
     */
    private function resolveExtendsChain(
        array &$discovered,
        DiscoveredClass $structure
    ): void {
        if ($structure->extends === null) {
            $structure->extendsChain = [];

            return;
        }

        if (! array_key_exists($structure->extends, $discovered)) {
            $structure->extendsChain = [$structure->extends];

            return;
        }

        /** @var DiscoveredClass $extendedStructure */
        $extendedStructure = $discovered[$structure->extends];

        if ($extendedStructure->extendsChain === null) {
            $this->resolveExtendsChain($discovered, $extendedStructure);
        }

        $structure->extendsChain = [$structure->extends, ...$extendedStructure->extendsChain];
    }

    /**
     * @param array<DiscoveredClass|DiscoveredEnum|DiscoveredInterface> $discovered
     */
    private function resolveImplementsChain(
        array &$discovered,
        DiscoveredClass|DiscoveredEnum|DiscoveredInterface $structure
    ): void {
        $implements = $structure instanceof DiscoveredInterface
            ? $structure->extends
            : $structure->implements;

        $chain = $implements;

        foreach ($implements as $implement) {
            if (! array_key_exists($implement, $discovered)) {
                $chain[] = $implement;

                continue;
            }
            /** @var DiscoveredInterface $implementedStructure */
            $implementedStructure = $discovered[$implement];

            if ($implementedStructure->extendsChain === null) {
                $this->resolveImplementsChain($discovered, $implementedStructure);
            }

            array_push($chain, ...$implementedStructure->extendsChain);
        }

        if ($structure instanceof DiscoveredClass
            && $structure->extends
            && array_key_exists($structure->extends, $discovered)
        ) {
            /** @var DiscoveredClass $extendedStructure */
            $extendedStructure = $discovered[$structure->extends] ?? null;

            if ($extendedStructure->implementsChain === null) {
                $this->resolveImplementsChain($discovered, $extendedStructure);
            }

            array_push($chain, ...$extendedStructure->implementsChain);
        }

        $chain = array_unique($chain);

        if ($structure instanceof DiscoveredInterface) {
            $structure->extendsChain = $chain;
        } else {
            $structure->implementsChain = $chain;
        }
    }
}
