<?php

namespace Cerbero\JsonParser;

use IteratorAggregate;
use Traversable;

/**
 * The JSON parser.
 *
 */
class Parser implements IteratorAggregate
{
    const SCALAR_CONST = 1 << 0;
    const SCALAR_STRING = 1 << 1;
    const SCALAR_VALUE = self::SCALAR_CONST | self::SCALAR_STRING;

    const OBJECT_START = 1 << 2;
    const OBJECT_END = 1 << 3;

    const ARRAY_START = 1 << 4;
    const ARRAY_END = 1 << 5;

    const COMMA = 1 << 6;
    const COLON = 1 << 7;

    const ANY_VALUE = self::OBJECT_START | self::ARRAY_START | self::SCALAR_VALUE;

    const AFTER_ARRAY_START = self::ANY_VALUE | self::ARRAY_END;
    const AFTER_ARRAY_VALUE = self::COMMA | self::ARRAY_END;

    const AFTER_OBJECT_START = self::SCALAR_STRING | self::OBJECT_END;
    const AFTER_OBJECT_VALUE = self::COMMA | self::OBJECT_END;

    /**
     * The token types.
     *
     * @var array
     */
    protected const TYPES = [
        'n' => self::SCALAR_CONST,
        't' => self::SCALAR_CONST,
        'f' => self::SCALAR_CONST,
        '-' => self::SCALAR_CONST,
        '0' => self::SCALAR_CONST,
        '1' => self::SCALAR_CONST,
        '2' => self::SCALAR_CONST,
        '3' => self::SCALAR_CONST,
        '4' => self::SCALAR_CONST,
        '5' => self::SCALAR_CONST,
        '6' => self::SCALAR_CONST,
        '7' => self::SCALAR_CONST,
        '8' => self::SCALAR_CONST,
        '9' => self::SCALAR_CONST,
        '"' => self::SCALAR_STRING,
        '{' => self::OBJECT_START,
        '}' => self::OBJECT_END,
        '[' => self::ARRAY_START,
        ']' => self::ARRAY_END,
        ',' => self::COMMA,
        ':' => self::COLON,
    ];

    /**
     * Instantiate the class.
     *
     * @param Lexer $lexer
     * @param Config $config
     */
    public function __construct(protected Lexer $lexer, protected Config $config)
    {
    }

    /**
     * Retrieve the JSON fragments
     *
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        foreach ($this->lexer as $token) {
            //
        }
    }
}
