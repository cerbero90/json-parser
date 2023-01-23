<?php

namespace Cerbero\JsonParser;

use Cerbero\JsonParser\Sources\Source;
use Cerbero\JsonParser\Tokens\Token;
use Cerbero\JsonParser\Tokens\Tokenizer;
use Cerbero\JsonParser\Tokens\Tokens;
use IteratorAggregate;
use Traversable;

/**
 * The JSON lexer.
 *
 * @implements IteratorAggregate<int, Token>
 */
final class Lexer implements IteratorAggregate
{
    /**
     * The parsing progress.
     *
     * @var Progress
     */
    private Progress $progress;

    /**
     * The current position.
     *
     * @var int
     */
    private int $position = 0;

    /**
     * Instantiate the class.
     *
     * @param Source $source
     */
    public function __construct(private Source $source)
    {
        $this->progress = new Progress();
    }

    /**
     * Retrieve the JSON fragments
     *
     * @return Traversable<int, Token>
     */
    public function getIterator(): Traversable
    {
        $buffer = '';
        $inString = $isEscaping = false;

        foreach ($this->source as $chunk) {
            for ($i = 0, $size = strlen($chunk); $i < $size; $i++, $this->position++) {
                $character = $chunk[$i];
                $inString = ($character == '"' && $inString && $isEscaping)
                    || ($character != '"' && $inString)
                    || ($character == '"' && !$inString);
                $isEscaping = $character == '\\' && !$isEscaping;

                if ($inString || !isset(Tokens::BOUNDARIES[$character])) {
                    $buffer .= $character;
                    continue;
                }

                if ($buffer != '') {
                    yield Tokenizer::instance()->toToken($buffer);
                    $buffer = '';
                }

                if (isset(Tokens::DELIMITERS[$character])) {
                    yield Tokenizer::instance()->toToken($character);
                }
            }
        }
    }

    /**
     * Retrieve the parsing progress
     *
     * @return Progress
     */
    public function progress(): Progress
    {
        return $this->progress->setCurrent($this->position)->setTotal($this->source->size());
    }
}
