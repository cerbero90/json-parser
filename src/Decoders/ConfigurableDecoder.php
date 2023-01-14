<?php

namespace Cerbero\JsonParser\Decoders;

use Cerbero\JsonParser\Config;

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
        $decoded = $this->config->decoder->decode((string) $value);

        if (!$decoded->succeeded) {
            call_user_func($this->config->onError, $decoded);
        }

        return $decoded->value;
    }
}
