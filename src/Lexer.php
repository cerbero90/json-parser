<?php

namespace Cerbero\JsonParser;

use Cerbero\JsonParser\Sources\Source;
use Cerbero\JsonParser\Tokens\Token;
use Cerbero\JsonParser\Tokens\Tokens;
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
     * The map of token instances by type.
     *
     * @var array<int, Token>
     */
    private array $tokensMap;

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
        $this->setTokensMap();
    }

    /**
     * Set the tokens map
     *
     * @return void
     */
    private function setTokensMap(): void
    {
        $instances = [];

        foreach (Tokens::MAP as $type => $class) {
            $this->tokensMap[$type] = $instances[$class] ??= new $class();
        }
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
                    $type = Tokens::TYPES[$buffer[0]];
                    yield $this->tokensMap[$type]->setValue($buffer);
                    $buffer = '';
                }

                if (isset(Tokens::DELIMITERS[$character])) {
                    $type = Tokens::TYPES[$character];
                    yield $this->tokensMap[$type]->setValue($character);
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
