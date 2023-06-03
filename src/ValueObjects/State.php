<?php

namespace Cerbero\JsonParser\ValueObjects;

use Cerbero\JsonParser\Pointers\Pointers;
use Cerbero\JsonParser\Tokens\CompoundBegin;
use Cerbero\JsonParser\Tokens\Parser;
use Cerbero\JsonParser\Tokens\Token;
use Cerbero\JsonParser\Tokens\Tokens;
use Closure;

/**
 * The JSON parsing state.
 *
 */
final class State
{
    /**
     * The JSON tree.
     *
     * @var Tree
     */
    private Tree $tree;

    /**
     * The JSON buffer.
     *
     * @var Parser|string
     */
    private Parser|string $buffer = '';

    /**
     * Whether an object key is expected.
     *
     * @var bool
     */
    public bool $expectsKey = false;

    /**
     * The expected token.
     *
     * @var int
     */
    public int $expectedToken = Tokens::COMPOUND_BEGIN;

    /**
     * Instantiate the class.
     *
     * @param Pointers $pointers
     * @param Closure $lazyLoad
     */
    public function __construct(private Pointers $pointers, private Closure $lazyLoad)
    {
        $this->tree = new Tree($pointers);
    }

    /**
     * Retrieve the JSON tree
     *
     * @return Tree
     */
    public function tree(): Tree
    {
        return $this->tree;
    }

    /**
     * Retrieve the current key of the JSON tree
     *
     * @return string|int
     */
    public function key(): string|int
    {
        return $this->tree->currentKey();
    }

    /**
     * Determine whether the parser can stop parsing
     *
     * @return bool
     */
    public function canStopParsing(): bool
    {
        return $this->pointers->wereFoundInTree($this->tree);
    }

    /**
     * Call the current pointer callback
     *
     * @param mixed $value
     * @param mixed $key
     * @return mixed
     */
    public function callPointer(mixed $value, mixed &$key): mixed
    {
        return $this->pointers->matching()->call($value, $key);
    }

    /**
     * Mutate state depending on the given token
     *
     * @param Token $token
     * @return void
     */
    public function mutateByToken(Token $token): void
    {
        $this->tree->traverseToken($token, $this->expectsKey);

        ///// find a way to skip this when we are processing the same compound for the second time
        ///// i.e. when the compound is lazy loaded and we are looping through it
        ///// maybe set flag in CompoundBegin::mutateState() when shouldLazyLoad == true
        if ($this->tree->isMatched() && ((!$this->expectsKey && $token->isValue()) || $this->tree->isDeep())) {
            $pointer = $this->pointers->markAsFound();
            $this->buffer = $token instanceof CompoundBegin && $pointer->isLazy()
                ? ($this->lazyLoad)()
                : $this->buffer . $token;

            // if ($token instanceof CompoundBegin && $pointer->isLazy()) {
            //     $this->buffer = ($this->lazyLoad)();
            //     // $token->shouldLazyLoad = true;
            // } else {
            //     /** @phpstan-ignore-next-line */
            //     $this->buffer .= $token;
            // }
        }

        $token->mutateState($this);
    }

    /**
     * Determine whether the buffer is getting lazy loaded
     *
     * @return bool
     */
    public function isLazyLoading(): bool
    {
        return $this->buffer instanceof Parser;
    }

    /**
     * Determine whether the buffer is ready to be yielded
     *
     * @return bool
     */
    public function shouldYield(): bool
    {
        return $this->buffer != '' && !$this->tree->isDeep();
    }

    /**
     * Retrieve the value from the buffer and reset it
     *
     * @return Parser|string
     */
    public function value(): Parser|string
    {
        $buffer = $this->buffer;

        $this->buffer = '';

        return $buffer;
    }
}
