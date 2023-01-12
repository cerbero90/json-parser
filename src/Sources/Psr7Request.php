<?php

namespace Cerbero\JsonParser\Sources;

use Cerbero\JsonParser\Concerns\GuzzleAware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Traversable;

/**
 * The PSR-7 request source.
 *
 * @property-read RequestInterface $source
 */
class Psr7Request extends Source
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
     * @throws \Cerbero\JsonParser\Exceptions\SourceException
     */
    public function getIterator(): Traversable
    {
        $this->requireGuzzle();

        $this->response = $this->sendRequest($this->source);

        return new Psr7Message($this->response, $this->config);
    }

    /**
     * Determine whether the JSON source can be handled
     *
     * @return bool
     */
    public function matches(): bool
    {
        return $this->source instanceof RequestInterface;
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
