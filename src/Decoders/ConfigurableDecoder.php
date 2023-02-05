<?php

namespace Cerbero\JsonParser\Decoders;

use Cerbero\JsonParser\Config;

use function call_user_func;

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
    public function __construct(private Config $config)
    {
    }

    /**
     * Decode the given value.
     *
     * @param string|int $value
     * @return mixed
     */
    public function decode(string|int $value): mixed
    {
        if (is_int($value)) {
            return $value;
        }

        $decoded = $this->config->decoder->decode($value);

        if (!$decoded->succeeded) {
            call_user_func($this->config->onError, $decoded);
        }

        return $decoded->value;
    }
}
