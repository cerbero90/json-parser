<?php

namespace Cerbero\JsonParser\Decoders;

use JsonException;

/**
 * The decoded value.
 *
 */
final class DecodedValue
{
    /**
     * Instantiate the class.
     *
     * @param mixed $value
     */
    private function __construct(
        public bool $succeeded,
        public mixed $value = null,
        public ?string $error = null,
        public ?int $code = null,
        public ?JsonException $exception = null,
        public ?string $json = null,
    ) {
    }

    /**
     * Retrieve a successfully decoded value
     *
     * @param mixed $value
     * @return static
     */
    public static function succeeded(mixed $value): static
    {
        return new static(true, $value);
    }

    /**
     * Retrieve a value failed to be decoded
     *
     * @param JsonException $e
     * @param string $json
     * @return static
     */
    public static function failed(JsonException $e, string $json): static
    {
        return new static(false, null, $e->getMessage(), $e->getCode(), $e, $json);
    }
}
