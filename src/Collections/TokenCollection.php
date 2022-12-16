<?php

namespace Spatie\StructureDiscoverer\Collections;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use PhpToken;
use Traversable;

class TokenCollection implements IteratorAggregate, Countable
{
    public function __construct(
        protected array $tokens,
    ) {
    }

    public function has(int $index): bool
    {
        return array_key_exists($index, $this->tokens);
    }

    public function get(int $index): ?PhpToken
    {
        return $this->has($index)
            ? $this->tokens[$index]
            : null;
    }

    /**
     * @return Traversable<int, PhpToken>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->tokens);
    }

    public function count(): int
    {
        return count($this->tokens);
    }
}
