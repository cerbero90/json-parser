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
    private readonly Progress $progress;

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
    public function __construct(private readonly Source $source)
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
        $tokenizer = Tokenizer::instance();

        foreach ($this->source as $chunk) {
            for ($i = 0, $size = strlen($chunk); $i < $size; $i++, $this->position++) {
                $character = $chunk[$i];
                $inString = ($character == '"') != $inString || $isEscaping;
                $isEscaping = $character == '\\' && !$isEscaping;

                if ($inString || !isset(Tokens::BOUNDARIES[$character])) {
                    $buffer == '' && !isset(Tokens::TYPES[$character]) && throw new SyntaxException($character);
                    $buffer .= $character;
                    continue;
                }

                if ($buffer != '') {
                    yield $tokenizer->toToken($buffer);
                    $buffer = '';
                }

                if (isset(Tokens::DELIMITERS[$character])) {
                    yield $tokenizer->toToken($character);
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
