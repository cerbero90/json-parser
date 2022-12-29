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
     * Decode the given JSON.
     *
     * @param string $json
     * @return mixed
     */
    public function decode(string $json): mixed
    {
        $decoded = $this->config->decoder->decode($json);

        if (!$decoded->succeeded) {
            call_user_func($this->config->onError, $decoded);
        }

        return $decoded->value;
    }
}
