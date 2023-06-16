<?php

namespace Cerbero\JsonParser\Decoders;

use Cerbero\JsonParser\Tokens\Parser;
use Cerbero\JsonParser\ValueObjects\Config;

/**
 * The configurable decoder.
 *
 */
final class ConfigurableDecoder
{
    /**
     * Instantiate the class.
     *
     * @param Config $config
     */
    public function __construct(private readonly Config $config)
    {
    }

    /**
     * Decode the given value.
     *
     * @param Parser|string|int $value
     * @return mixed
     */
    public function decode(Parser|string|int $value): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        $decoded = $this->config->decoder->decode($value);

        if (!$decoded->succeeded) {
            ($this->config->onDecodingError)($decoded);
        }

        return $decoded->value;
    }
}
