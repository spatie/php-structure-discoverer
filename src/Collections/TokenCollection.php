<?php

namespace Spatie\StructureDiscoverer\Collections;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use ParseError;
use PhpToken;
use Traversable;

/**
 * @implements IteratorAggregate<int, PhpToken>
 */
class TokenCollection implements IteratorAggregate, Countable
{
    /**
     * @param array<PhpToken> $tokens
     */
    public function __construct(
        protected array $tokens,
    ) {
    }

    public static function fromCode(string $code): self
    {
        try {
            $tokens = PhpToken::tokenize($code, TOKEN_PARSE);
        } catch (ParseError) {
            $tokens = [];
        }

        return new self(
            array_values(array_filter($tokens, fn (PhpToken $token) => ! $token->is([T_COMMENT, T_DOC_COMMENT, T_WHITESPACE])))
        );
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
