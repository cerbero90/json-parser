<?php

namespace Cerbero\JsonParser\Tokens;

/**
 * The tokens related information.
 *
 */
final class Tokens
{
    public const SCALAR_CONST = 1 << 0;
    public const SCALAR_STRING = 1 << 1;

    public const OBJECT_BEGIN = 1 << 2;
    public const OBJECT_END = 1 << 3;

    public const ARRAY_BEGIN = 1 << 4;
    public const ARRAY_END = 1 << 5;

    public const COMMA = 1 << 6;
    public const COLON = 1 << 7;

    public const COMPOUND_BEGIN = self::OBJECT_BEGIN | self::ARRAY_BEGIN;
    public const COMPOUND_END = self::OBJECT_END | self::ARRAY_END;

    public const VALUE_SCALAR = self::SCALAR_CONST | self::SCALAR_STRING;
    public const VALUE_ANY = self::COMPOUND_BEGIN | self::VALUE_SCALAR;

    public const AFTER_ARRAY_BEGIN = self::VALUE_ANY | self::ARRAY_END;
    public const AFTER_ARRAY_VALUE = self::COMMA | self::ARRAY_END;

    public const AFTER_OBJECT_BEGIN = self::SCALAR_STRING | self::OBJECT_END;
    public const AFTER_OBJECT_VALUE = self::COMMA | self::OBJECT_END;

    /**
     * The token types.
     *
     * @var array<string|int, int>
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
        '{' => self::OBJECT_BEGIN,
        '}' => self::OBJECT_END,
        '[' => self::ARRAY_BEGIN,
        ']' => self::ARRAY_END,
        ',' => self::COMMA,
        ':' => self::COLON,
    ];

    /**
     * The token boundaries.
     *
     * @var array<string, bool>
     */
    public const BOUNDARIES = [
        '{' => true,
        '}' => true,
        '[' => true,
        ']' => true,
        ',' => true,
        ':' => true,
        ' ' => true,
        "\n" => true,
        "\r" => true,
        "\t" => true,
        "\xEF" => true,
        "\xBB" => true,
        "\xBF" => true,
    ];

    /**
     * The structural boundaries.
     *
     * @var array<string, bool>
     */
    public const DELIMITERS = [
        '{' => true,
        '}' => true,
        '[' => true,
        ']' => true,
        ',' => true,
        ':' => true,
    ];

    /**
     * The tokens class map.
     *
     * @var array<int, class-string<Token>>
     */
    public const MAP = [
        self::COMMA => Comma::class,
        self::OBJECT_BEGIN => CompoundBegin::class,
        self::ARRAY_BEGIN => CompoundBegin::class,
        self::OBJECT_END => CompoundEnd::class,
        self::ARRAY_END => CompoundEnd::class,
        self::COLON => Colon::class,
        self::SCALAR_CONST => Constant::class,
        self::SCALAR_STRING => ScalarString::class,
    ];
}
