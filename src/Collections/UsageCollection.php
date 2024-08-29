<?php

namespace Spatie\StructureDiscoverer\Collections;

use ArrayIterator;
use IteratorAggregate;
use Spatie\StructureDiscoverer\Data\Usage;
use Traversable;

/**
 * @implements IteratorAggregate<Usage>
 */
class UsageCollection implements IteratorAggregate
{
    /**
     * @param  array<Usage> $usages
     */
    public function __construct(
        public array $usages = [],
    ) {
    }

    public function add(
        Usage ...$usages
    ): self {
        array_push($this->usages, ...$usages);

        return $this;
    }

    public function findForAlias(string $alias): ?Usage
    {
        foreach ($this->usages as $usage) {
            if ($usage->name === $alias) {
                return $usage;
            }
        }

        return null;
    }

    public function findFcqnForIdentifier(
        string $identifier,
        string $namespace,
    ): string {
        if ($usage = $this->findForAlias($identifier)) {
            return $usage->fcqn;
        }

        if (empty($namespace)) {
            return $identifier;
        }

        return "{$namespace}\\{$identifier}";
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->usages);
    }
}
