<?php

namespace Cerbero\JsonParser\Tokens;

/**
 * The tokens related information.
 *
 */
class Tokens
{
    public const SCALAR_CONST = 1 << 0;
    public const SCALAR_STRING = 1 << 1;
    public const SCALAR_VALUE = self::SCALAR_CONST | self::SCALAR_STRING;

    public const OBJECT_START = 1 << 2;
    public const OBJECT_END = 1 << 3;

    public const ARRAY_START = 1 << 4;
    public const ARRAY_END = 1 << 5;

    public const COMMA = 1 << 6;
    public const COLON = 1 << 7;

    public const ANY_VALUE = self::OBJECT_START | self::ARRAY_START | self::SCALAR_VALUE;

    public const AFTER_ARRAY_START = self::ANY_VALUE | self::ARRAY_END;
    public const AFTER_ARRAY_VALUE = self::COMMA | self::ARRAY_END;

    public const AFTER_OBJECT_START = self::SCALAR_STRING | self::OBJECT_END;
    public const AFTER_OBJECT_VALUE = self::COMMA | self::OBJECT_END;

    /**
     * The token types.
     *
     * @var array
     */
    public const TYPES = [
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
     * The token boundaries.
     *
     * @var array
     */
    public const BOUNDARIES = [
        "\xEF" => true,
        "\xBB" => true,
        "\xBF" => true,
        "\n" => true,
        "\r" => true,
        "\t" => true,
        ' ' => true,
        '{' => true,
        '}' => true,
        '[' => true,
        ']' => true,
        ':' => true,
        ',' => true,
    ];

    /**
     * The structural boundaries.
     *
     * @var array
     */
    public const DELIMITERS = [
        '{' => true,
        '}' => true,
        '[' => true,
        ']' => true,
        ':' => true,
        ',' => true,
    ];

    /**
     * The tokens class map.
     *
     * @var array
     */
    public const MAP = [
        //
    ];
}
