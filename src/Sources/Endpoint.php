<?php

namespace Cerbero\JsonParser\Sources;

use Cerbero\JsonParser\Concerns\DetectsEndpoints;
use Cerbero\JsonParser\Concerns\GuzzleAware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Traversable;

use function is_string;

/**
 * The endpoint source.
 *
 * @property-read UriInterface|string $source
 */
class Endpoint extends Source
{
    use DetectsEndpoints;
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
     * Retrieve the endpoint response
     *
     * @return ResponseInterface
     * @throws \Cerbero\JsonParser\Exceptions\GuzzleRequiredException
     */
    protected function response(): ResponseInterface
    {
        $this->requireGuzzle();

        return $this->response ??= $this->fetchResponse();
    }

    /**
     * Retrieve the fetched HTTP response
     *
     * @return ResponseInterface
     */
    protected function fetchResponse(): ResponseInterface
    {
        return $this->getJson($this->source);
    }

    /**
     * Determine whether the JSON source can be handled
     *
     * @return bool
     */
    public function matches(): bool
    {
        /** @phpstan-ignore-next-line */
        return (is_string($this->source) || $this->source instanceof UriInterface) && $this->isEndpoint($this->source);
    }

    /**
     * Retrieve the calculated size of the JSON source
     *
     * @return int|null
     * @throws \Cerbero\JsonParser\Exceptions\GuzzleRequiredException
     */
    protected function calculateSize(): ?int
    {
        return $this->response()->getBody()->getSize();
    }
}
