<?php

namespace Spatie\LaravelAutoDiscoverer\Collections;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Spatie\LaravelAutoDiscoverer\Data\Token;
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

    public function get(int $index): ?Token
    {
        return $this->has($index)
            ? $this->tokens[$index]
            : null;
    }

    /**
     * @return Traversable<int, Token>
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
