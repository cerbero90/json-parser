<?php

namespace Cerbero\JsonParser\Decoders;

use Throwable;

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
        public ?Throwable $exception = null,
        public ?string $json = null,
    ) {
    }

    /**
     * Retrieve a successfully decoded value
     *
     * @param mixed $value
     * @return self
     */
    public static function succeeded(mixed $value): self
    {
        return new self(true, $value);
    }

    /**
     * Retrieve a value failed to be decoded
     *
     * @param Throwable $e
     * @param string $json
     * @return self
     */
    public static function failed(Throwable $e, string $json): self
    {
        return new self(false, null, $e->getMessage(), $e->getCode(), $e, $json);
    }
}
