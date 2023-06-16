<?php

namespace Cerbero\JsonParser\Sources;

use Psr\Http\Message\StreamInterface;

/**
 * The JSON stream wrapper.
 *
 * @codeCoverageIgnore
 * @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
 */
final class StreamWrapper
{
    /**
     * The name of the stream wrapper.
     *
     * @var string
     */
    public const NAME = 'cerbero-json-parser';

    /**
     * The stream context.
     *
     * @var resource
     */
    public mixed $context;

    /**
     * The PSR-7 stream.
     *
     * @var StreamInterface
     */
    private $stream;

    /**
     * Open the stream
     *
     * @param string $path
     * @param string $mode
     * @param int $options
     * @param mixed $opened_path
     * @return bool
     *
     * @scrutinizer ignore-unused
     */
    public function stream_open(string $path, string $mode, int $options, &$opened_path): bool
    {
        $options = stream_context_get_options($this->context);

        $this->stream = $options[self::NAME]['stream'] ?? null;

        return $this->stream instanceof StreamInterface && $this->stream->isReadable();
    }

    /**
     * Determine whether the pointer is at the end of the stream
     *
     * @return bool
     */
    public function stream_eof(): bool
    {
        return $this->stream->eof();
    }

    /**
     * Read from the stream
     *
     * @param int $count
     * @return string
     */
    public function stream_read(int $count): string
    {
        return $this->stream->read($count);
    }
}
