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
        $this->requireGuzzle();

        $this->response = $this->getJson($this->source);

        return new Psr7Message($this->response, $this->config);
    }

    /**
     * Determine whether the JSON source can be handled
     *
     * @return bool
     */
    public function matches(): bool
    {
        // @phpstan-ignore-next-line
        return (is_string($this->source) || $this->source instanceof UriInterface) && $this->isEndpoint($this->source);
    }

    /**
     * Retrieve the calculated size of the JSON source
     *
     * @return int|null
     */
    protected function calculateSize(): ?int
    {
        return $this->response?->getBody()->getSize();
    }
}
