<?php

namespace Spatie\StructureDiscoverer\Resolvers;

use Spatie\StructureDiscoverer\Data\DiscoveredClass;
use Spatie\StructureDiscoverer\Data\DiscoveredEnum;
use Spatie\StructureDiscoverer\Data\DiscoveredInterface;

class StructureChainResolver
{
    public function execute(array &$discovered)
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

    private function resolveExtendsChain(
        array &$discovered,
        DiscoveredClass $structure
    ): void {
        /** @var DiscoveredClass $extendedStructure */
        $extendedStructure = $discovered[$structure->extends] ?? null;

        if ($extendedStructure === null) {
            $structure->extendsChain = [];

            return;
        }

        if ($extendedStructure->extendsChain === null) {
            $this->resolveExtendsChain($discovered, $extendedStructure);
        }

        $structure->extendsChain = [$structure->extends, ...$extendedStructure->extendsChain];
    }

    private function resolveImplementsChain(
        array &$discovered,
        DiscoveredClass|DiscoveredEnum|DiscoveredInterface $structure
    ): void {
        $implements = $structure instanceof DiscoveredInterface
            ? $structure->extends
            : $structure->implements;

        $chain = $implements;

        foreach ($implements as $implement) {
            /** @var DiscoveredInterface $implementedStructure */
            $implementedStructure = $discovered[$implement] ?? null;

            if ($implementedStructure === null) {
                continue;
            }

            if($implementedStructure->extendsChain === null){
                $this->resolveImplementsChain($discovered, $implementedStructure);
            }

            array_push($chain, ...$implementedStructure->extendsChain);
        }

        if ($structure instanceof DiscoveredInterface) {
            $structure->extendsChain = $chain;
        } else {
            $structure->implementsChain = $chain;
        }
    }
}
