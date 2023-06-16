<?php

namespace Cerbero\JsonParser\Exceptions;

use Exception;

/**
 * The exception thrown when the JSON syntax is not valid.
 *
 */
final class SyntaxException extends Exception implements JsonParserException
{
    /**
     * The error position.
     *
     * @var int|null
     */
    public ?int $position = null;

    /**
     * Instantiate the class
     *
     * @param string $value
     */
    public function __construct(public readonly string $value)
    {
        parent::__construct("Syntax error: unexpected '$value'");
    }

    /**
     * Set the error position
     *
     * @param int $position
     * @return self
     */
    public function setPosition(int $position): self
    {
        $this->position = $position;
        $this->message .= " at position {$position}";

        return $this;
    }
}
