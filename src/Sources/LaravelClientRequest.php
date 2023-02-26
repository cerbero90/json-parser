<?php

namespace Cerbero\JsonParser\Sources;

use Cerbero\JsonParser\Concerns\GuzzleAware;
use Illuminate\Http\Client\Request;
use Psr\Http\Message\ResponseInterface;
use Traversable;

/**
 * The Laravel client request source.
 *
 * @property-read Request $source
 */
class LaravelClientRequest extends Source
{
    use GuzzleAware;

    /**
     * The endpoint response.
     *
     * @var ResponseInterface|null
     */
    protected ?ResponseInterface $response;

    /**
     * Retrieve the JSON fragments
     *
     * @return Traversable<int, string>
     * @throws \Cerbero\JsonParser\Exceptions\GuzzleRequiredException
     */
    public function getIterator(): Traversable
    {
        return new Psr7Message($this->response(), $this->config);
    }

    /**
     * Retrieve the response of the Laravel request
     *
     * @return ResponseInterface
     * @throws \Cerbero\JsonParser\Exceptions\GuzzleRequiredException
     */
    protected function response(): ResponseInterface
    {
        $this->requireGuzzle();

        return $this->response ??= $this->sendRequest($this->source->toPsrRequest());
    }

    /**
     * Determine whether the JSON source can be handled
     *
     * @return bool
     */
    public function matches(): bool
    {
        return $this->source instanceof Request;
    }

    /**
     * Retrieve the calculated size of the JSON source
     *
     * @return int|null
     */
    protected function calculateSize(): ?int
    {
        return $this->response()->getBody()->getSize();
    }
}
