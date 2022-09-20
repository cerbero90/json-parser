<?php

namespace Cerbero\JsonParser;

use Cerbero\JsonParser\Tokens\Token;
use IteratorAggregate;
use Traversable;

/**
 * The JSON tree.
 *
 */
class Tree implements IteratorAggregate
{
    /**
     * The original JSON tree.
     *
     * @var array
     */
    protected array $original = [];

    /**
     * The wildcarded JSON tree.
     *
     * @var array
     */
    protected array $wildcarded = [];

    /**
     * The JSON tree depth.
     *
     * @var int
     */
    protected int $depth = 0;

    /**
     * Traverse the given token
     *
     * @param Token $token
     * @return void
     */
    public function traverse(Token $token): void
    {
        $this->original[$this->depth] = $token->value();
        $this->wildcarded[$this->depth] = is_int($token->value()) ? '-' : $token->value();
    }

    /**
     * Retrieve the original JSON tree
     *
     * @return array
     */
    public function original(): array
    {
        return $this->original;
    }

    /**
     * Retrieve the wildcarded JSON tree
     *
     * @return array
     */
    public function wildcarded(): array
    {
        return $this->wildcarded;
    }

    /**
     * Retrieve the JSON tree depth
     *
     * @return int
     */
    public function depth(): int
    {
        return $this->depth;
    }

    /**
     * Increase the tree depth
     *
     * @return void
     */
    public function deepen(): void
    {
        $this->depth++;
    }

    /**
     * Decrease the tree depth
     *
     * @return void
     */
    public function emerge(): void
    {
        $this->depth--;
    }

    /**
     * Retrieve the original tree iterator
     *
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        yield from $this->original();
    }
}
