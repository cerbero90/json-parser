<?php

namespace Cerbero\JsonParser\Tokens;

use Cerbero\JsonParser\Exceptions\SyntaxException;
use Cerbero\JsonParser\Sources\Source;
use Cerbero\JsonParser\Tokens\Token;
use Cerbero\JsonParser\Tokens\Tokenizer;
use Cerbero\JsonParser\Tokens\Tokens;
use Cerbero\JsonParser\ValueObjects\Progress;
use IteratorAggregate;
use Traversable;

use function strlen;

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
    private int $position = 1;

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
     * @return \Generator<int, Token>
     */
    public function getIterator(): Traversable
    {
        $buffer = '';
        $inString = $isEscaping = false;

        foreach ($this->source as $chunk) {
            for ($i = 0, $size = strlen($chunk); $i < $size; $i++, $this->position++) {
                $isQuote = '"' == $character = $chunk[$i];
                $inString = $isQuote != $inString || ($isQuote && $inString && $isEscaping);
                $isEscaping = $character == '\\' && !$isEscaping;
                $shouldBuffer = $inString || !isset(Tokens::BOUNDARIES[$character]);

                if ($shouldBuffer && $buffer == '' && !isset(Tokens::TYPES[$character])) {
                    throw new SyntaxException($character);
                }

                if ($shouldBuffer) {
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
     * Retrieve the current position
     *
     * @return int
     */
    public function position(): int
    {
        return $this->position;
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
