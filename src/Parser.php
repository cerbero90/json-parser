<?php

namespace Cerbero\JsonParser;

use Cerbero\JsonParser\Decoders\ConfigurableDecoder;
use Cerbero\JsonParser\Exceptions\SyntaxException;
use Cerbero\JsonParser\Sources\Source;
use IteratorAggregate;
use Traversable;

/**
 * The JSON parser.
 *
 * @implements IteratorAggregate<string|int, mixed>
 */
final class Parser implements IteratorAggregate
{
    /**
     * The decoder handling potential errors.
     *
     * @var ConfigurableDecoder
     */
    private ConfigurableDecoder $decoder;

    /**
     * Instantiate the class.
     *
     * @param Lexer $lexer
     * @param Config $config
     */
    public function __construct(private Lexer $lexer, private Config $config)
    {
        $this->decoder = new ConfigurableDecoder($config);
    }

    /**
     * Instantiate the class statically
     *
     * @param Source $source
     * @return self
     */
    public static function for(Source $source): self
    {
        return new self(new Lexer($source), $source->config());
    }

    /**
     * Retrieve the JSON fragments
     *
     * @return Traversable<string|int, mixed>
     */
    public function getIterator(): Traversable
    {
        $state = new State($this->config->pointers);

        foreach ($this->lexer as $token) {
            if (!$token->matches($state->expectedToken)) {
                throw new SyntaxException($token, $this->lexer->position());
            }

            $state->mutateByToken($token);

            if (!$token->endsChunk() || $state->treeIsDeep()) {
                continue;
            }

            if ($state->hasBuffer()) {
                /** @var string|int $key */
                $key = $this->decoder->decode($state->key());
                $value = $this->decoder->decode($state->value());

                yield $key => $state->callPointer($value, $key);
            }

            if ($state->canStopParsing()) {
                break;
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
        return $this->lexer->progress();
    }
}
